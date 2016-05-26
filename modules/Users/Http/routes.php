<?php

Route::group(['prefix' => 'users', 'namespace' => 'Modules\Users\Http\Controllers', 'middleware' => ['auth', 'admin']], function()
{
	Route::get('/', ['as' => 'users.index', 'uses' => 'UsersController@index']);
	Route::get('/{user}', ['as' => 'users.show', 'uses' => 'UsersController@show']);
	Route::DELETE('/{user}', ['as' => 'users.destroy', 'uses' => 'UsersController@destroy']);
	Route::get('/{user}/edit', ['as' => 'users.edit', 'uses' => 'UsersController@edit']);
	Route::PUT('/{user}', ['as' => 'users.update', 'uses' => 'UsersController@update']);
	Route::PATCH('/{user}', 'UsersController@update');
});