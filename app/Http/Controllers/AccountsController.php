<?php

namespace App\Http\Controllers;

use App\Account;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;
use Mail;

class AccountsController extends Controller
{

    public function getAccountManager(){
        return view('guest.auth.temp.accounts');
    }

    public function getFirstLogin()
    {
        return redirect()->route('user::dashboard');
    }

    public function sendVerifMail(/*Request $request*/){

        $account = Account::where('email', Input::get('otherEmail'))->first();

        var_dump( Auth::user()->confirmation_code);


        if($account){

            Mail::send('user.email.verify', ['confirmation_code' => Auth::user()->confirmation_code, 'user_id' => $account->user->id], function($message) {
                $message->to(Input::get('otherEmail'))->subject('Verify your email address');
            });

            Flash::message('Thanks for signing up! Please check your email.');
        }


        return redirect()->route('user::dashboard');
    }

    public function confirm($confirmation_code, $user_id)
    {
        if( ! $confirmation_code)
        {
            throw new InvalidConfirmationCodeException;
        }

        $account = Account::whereConfirmationCode($confirmation_code)->first();

        if ( ! $account)
        {
            throw new InvalidConfirmationCodeException;
        }

        $account->confirmation_code = null;
        $tempUser = $account->user;

        $account->user_id = $user_id;
        $account->save();
        $tempUser->forceDelete();

//        Flash::message('You have successfully verified your account.');
//        Session::flash('message', 'You have successfully verified your account.');
        Redirect::to('/')->with('message', 'success|Record updated.');
    }

}