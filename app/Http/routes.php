<?php


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Intervention\Image\Facades\Image;
use App\Classes\ApplicationServer\Server;
use Modules\Experiments\Entities\Experiment;
use Modules\Experiments\Entities\ServerExperiment;

Route::get('/', function() {
    return Redirect::to('auth/login');
});

// Authentication routes...

Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@login');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Account routes...
Route::get('accounts/firstLogin', 'AccountsController@getFirstLogin');
Route::post('accounts/sendVerifMain', 'AccountsController@sendVerifMail');
Route::get('accounts/verify/{confirmationCode}/userId/{userId}', [
    'as' => 'confirmation_path',
    'uses' => 'AccountsController@confirm'
]);

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');

// Password routes

Route::get('password/email', 'Auth\PasswordController@getEmail');

// LDAP authentication

//Route::post('auth/ldap', ['as' => 'auth.ldap', 'uses' => 'Auth\AuthController@postLdap']);

// 3rd party authentication
Route::get('auth/provider/{provider}/', 'Auth\AuthController@redirectToProvider');
Route::get('auth/provider/{provider}/callback', 'Auth\AuthController@handleProviderCallback');


// If logged in
Route::group(['as'  =>  'user::', 'middleware'  =>  'auth'], function() {
    Route::get('dashboard', ['as' => 'dashboard', 'uses' => 'DashboardController@getDashboard']);
    Route::get('dashboard/settings', ['as' => 'dashboard.settings', 'uses' => 'DashboardController@getDashboardSettings']);
    Route::get('linkAccounts', ['as' => 'linkAccounts', 'uses' => 'AccountsController@getAccountManager']);

    Route::get('images/profile/{userID}', function($userID)
    {
        if(\App\User::find($userID)->avatar){
            $filepath = storage_path() . '/user_uploads/'.$userID.'/' . \App\User::find($userID)->avatar;
        }else{
            $filepath = public_path() . '/pictures/default-avatar.png';
        }

        $img = Image::make($filepath);

// add callback functionality to retain maximal original image size
        $img->resize(200, 200, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        return $img->response();
        //return Response::download($filepath);
    });
});

Route::get('test/data', function() {

    // $server = new Server("192.168.100.110");
    // $res = $server->queueExperiment([
    //         "device" => "tos1a",
    //         "software" => "openloop",
    //         "input" => [
    //             "start" => [
    //                 "c_fan" => 30,
    //                 "c_led" => 50,
    //                 "c_lamp" => 50,
    //                 "t_sim" => 5,
    //                 "s_rate" => 50
    //             ]
    //         ]
    //     ]);
    // dd($res);
    $instance = ServerExperiment::first();
    $report = new App\Services\ReportService();
    $report->create($instance,["hee"]);
});