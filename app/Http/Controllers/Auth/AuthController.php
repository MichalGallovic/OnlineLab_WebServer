<?php

namespace App\Http\Controllers\Auth;

use App\Account;
use App\LoginData;
use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use DB;
use Illuminate\Http\Request;
use Auth;
use Socialite;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => ['getLogout', 'handleProviderCallback']]);
    }

    protected $loginPath = '/auth/login';
    protected $redirectPath = '/dashboard';

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = new User;
        $user->name = $data['name'];
        $user->surname = $data['surname'];
        $user->role = 'user';
        $user->save();

        $account = new Account;
        $account->user()->associate($user);
        $account->password = bcrypt($data['password']);
        $account->type = 'local';
        $account->email = $data['email'];
        $account->save();

        return $account;
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        $items = LoginData::select('country', DB::raw('count(id) as total'))->groupby('country')->lists('total', 'country');
        return view('guest.auth.temp.login', compact('items'));
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        return view('guest.auth.temp.register');
    }

    public function login(Request $request){
        //return var_dump($request->email);
        if(isset($request->local)){
            return $this->postLogin($request);
        }elseif(isset($request->ldap)){
            return $this->postLdap($request);
        }
    }

    public function postRegister(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        Auth::login($this->create($request->all()));

        return redirect()->route('profile.settings');
    }

    public function postLdap($credentials){
        $ldaprdn  = 'uid='.$credentials->email.', ou=People, DC=stuba, DC=sk';
        $dn  = 'ou=People, DC=stuba, DC=sk';

        $ldapconn = ldap_connect("ldap.stuba.sk");
        // connect to ldap server
        ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

        // binding to ldap server
        @$ldapbind = ldap_bind($ldapconn, $ldaprdn, $credentials->password);

        // verify binding
        if ($ldapbind) {
            $filter="uid=".$credentials->email;
            //$justthese = array("givenname","employeetype","surname","mail","faculty","cn");
            $justthese = array("givenname","surname","mail");

            $sr=ldap_search($ldapconn, $dn, $filter, $justthese);

            $info = ldap_get_entries($ldapconn, $sr);
            ldap_close($ldapconn);
            //return $info;
            $account =  Account::where('type', 'ldap')->whereIn('email', $info[0]['mail'])->first();

            if(!$account){
                $user = new User;
                $user->name = $info[0]['givenname'][0];
                $user->surname = $info[0]['sn'][0];
                $user->save();

                $account = new Account;
                $account->type = 'ldap';
                $account->email = $credentials->email . '@stuba.sk';//$info[0]['mail'][0];
                $account->confirmation_code = str_random(30);
                $account->user()->associate($user);
                $account->save();

                 Auth::login($account, true);

                $this->logLogin($account);

                return redirect()->route('user::linkAccounts');
            }

            Auth::login($account, true);

            $this->logLogin($account);

            //return var_dump($info);
            return redirect()->route('user::dashboard');
        } else if (ldap_get_option($ldapconn, 0x0032, $extended_error)) {
            return redirect('auth/login')->with('fail', "Error Binding to LDAP: $extended_error")->withInput();
        } else {
            return redirect('auth/login')->with('fail', 'Incorrect LDAP credentials')->withInput();
        }
    }

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->user();

        if(Auth::check()){
            $account = Account::create([
                'user_id' => Auth::user()->user->id,
                'email' => $user->email,
                'type' => $provider,
                'confirmation_code' => str_random(30),
                'login_id' => $user->id,
            ]);

            $this->logLogin($account);

            return redirect()->route('profile.settings', compact('user'));
        }else{
           $account = $this->findOrCreateUser($user, $provider);
           Auth::login($account, true);
        }

        $this->logLogin($account);

        if($account->firstLogin){
            return redirect()->route('user::linkAccounts');
        }

        return redirect()->route('user::dashboard');
    }

    private function findOrCreateUser($user, $provider){
        $account =  Account::where(['type' => $provider,'login_id' => $user->id])->first();

        if (!$account){

            $authUser =  User::create([
                'name' => isset($user->givenName) ? $user->givenName : strtok($user->name, " "),
                'surname' => isset($user->faimlyName) ? $user->familyName : substr($user->name, strpos($user->name, ' ') + 1)
            ]);

            $account = Account::create([
                'login_id' => $user->id,
                'user_id' => $authUser->id,
                'email' => $user->email,
                'type' => $provider,
                'confirmation_code' => str_random(30)
            ]);

        }else{
            $account->firstLogin = false;
        }

        return $account;
    }

    private function logLogin($account){

        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        else
        {
            $ip = $remote;
        }


        $ch = curl_init();
        // Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set the url
        //curl_setopt($ch, CURLOPT_URL,"ipinfo.io/217.67.31.67");
        curl_setopt($ch, CURLOPT_URL,"ipinfo.io/".$ip);

        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if(array_key_exists('loc', $result) && array_key_exists('country', $result)){
            $loginData = new LoginData();
            $loginData->account()->associate($account);
            $loginData->setLocationAttribute($result['loc']);
            $loginData->ip = $ip;
            $loginData->save();
        }

    }

}
