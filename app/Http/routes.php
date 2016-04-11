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



Route::get('/', function() {
    return Redirect::to('auth/login');
});

// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');

// Password routes

Route::get('password/email', 'Auth\PasswordController@getEmail');


// If logged in
Route::group(['as'  =>  'user::', 'middleware'  =>  'auth'], function() {
    Route::get('dashboard', ['as' => 'dashboard.home', 'uses' => 'DashboardController@getDashboard']);
    Route::get('dashboard/settings', ['as' => 'dashboard.settings', 'uses' => 'DashboardController@getDashboardSettings']);
});

Route::get('test', function() {
    app('Illuminate\Contracts\Bus\Dispatcher')->dispatch(new \App\Jobs\TestJob());
});