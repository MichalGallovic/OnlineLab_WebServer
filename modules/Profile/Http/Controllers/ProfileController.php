<?php namespace Modules\Profile\Http\Controllers;

use App\Account;
use Pingpong\Modules\Routing\Controller;
use Auth;
use Illuminate\Support\Facades\Input;
use Validator;
use Illuminate\Http\Request;
use Socialite;
use App\User;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller {
	
	public function getSettings()
	{
		$user = Auth::user()->user;

		return view('profile::settings', compact('user'));
	}

	public function update(Request $request){

		// Fetch all request data.
		//$data = Input::all();

		$user = Auth::user()->user;

		foreach($user->accounts as $account){
			$account->notify = false;
			$account->save();
		}

		if(isset($request->account)){
			foreach($request->account as $accountId){
				$account = Account::find($accountId);
				$account->notify = true;
				$account->save();
			}
		}

		$rules = array(
			//'username' => 'required|alpha_num|min:3|max:32',
			'email' => 'required|email',
			'password' => 'required|min:3|confirmed',
			'password_confirmation' => 'required|min:3'
		);

		// Create a new validator instance.
		$validator = Validator::make($request->all(), $rules);


		if ($validator->passes()) {
			if ($request->file('avatar') && $request->file('avatar')->isValid()) {
				$request->file('avatar')->move(storage_path().'/user_uploads/'.$user->id.'/', $request->file('avatar')->getClientOriginalName()); // uploading file to given path
				$userUpdate['avatar'] = $request->file('avatar')->getClientOriginalName();
			}

			$userUpdate=$request->all();

			if(!$user->hasAccount('local')){
				$acc = new Account();
				$acc->user()->associate($user);

			}else{
				$acc = $user->getAccount('local');
			}

			$acc->email = $request->email;
			$acc->password = Hash::make($request->password);
			$acc->save();

			foreach ($user->accounts as $account) {
				if($account->email == $request->email && $account->type != 'local'){
					$account->password = Hash::make($request->password);
					$account->save();
				}
			}


			if($user->update($userUpdate)){
				return redirect()->route('profile.settings', compact('user'))->with('success', 'Profile was succesfully changed.');
			}else{
				return redirect()->route('profile.settings', compact('user'))->with('fail', 'An error has occured during profile update.');
			}

		}else{
			//var_dump($validator->passes());
			return redirect()->route('profile.settings', compact('user'))->withInput()->withErrors($validator)->with('fail', 'Please fill the form correctly.');
		}
	}

	public function destroyAccount($account_id){
		if(Auth::user()->id == $account_id){
			if(Auth::user()->user->accounts->count() > 1){
				foreach (Auth::user()->user->accounts as $account) {
					if($account->id != $account_id){
						Auth::login($account);
						break;
					}
				}
				if(Account::find($account_id)->delete()){
					return redirect()->route('profile.settings', compact('user'))->with('success', 'Profile was succesfully changed.');
				}
			}
			return redirect()->route('profile.settings', compact('user'))->with('fail', 'An error has occured during profile update.');
		}
	}

	public function addLdap(){
		$validator = Validator::make(Input::all(), array(
			'ldap_login' => 'required',
			'ldap_password' => 'required'
		));

		if($validator->fails()){
			return redirect()->route('profile.settings')->withInput()->withErrors($validator)->with('modal', '#ldap_form');
		}
		else{

			$credentials=Input::all();

			$ldaprdn  = 'uid='.$credentials['ldap_login'].', ou=People, DC=stuba, DC=sk';
			$dn  = 'ou=People, DC=stuba, DC=sk';

			// connect to ldap server
			$ldapconn = ldap_connect("ldap.stuba.sk") or die("Could not connect to LDAP server.");
			ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

			if ($ldapconn) {

				// binding to ldap server
				$ldapbind = ldap_bind($ldapconn, $ldaprdn, $credentials['ldap_password']);

				// verify binding
				if ($ldapbind) {
					$filter="uid=".$credentials['ldap_login'];
					$justthese = array("givenname","surname","mail");

					$sr=ldap_search($ldapconn, $dn, $filter, $justthese);

					$info = ldap_get_entries($ldapconn, $sr);

					$account = new Account;
					$account->type = 'ldap';
					$account->email = $info[0]['mail'][0];
					$account->user()->associate(Auth::user()->user);
					if($account->save()){
						return redirect()->route('profile.settings')->with('success', 'The account was connected.');
					}else{
						return redirect()->route('profile.settings')->with('fail', 'An error occured while connecting the accounts.');
					}
				} else {
					return redirect()->route('profile.settings')->with('fail', 'Login or passowrd was incorrect');
				}
			}

		}
	}

	public function redirectToProvider($provider)
	{
		if(!config("services.$provider")) abort('404');
		return Socialite::with($provider)->redirect();
	}

}