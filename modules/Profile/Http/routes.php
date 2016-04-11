<?php

Route::group(['prefix' => 'profile', 'namespace' => 'Modules\Profile\Http\Controllers'], function()
{
	Route::get('/settings', ['as' => 'profile.settings', 'uses' => 'ProfileController@getSettings']);
	Route::PUT('/', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
	Route::PATCH('/', 'ProfileController@update');
	Route::POST('/', ['as' => 'profile.photo', 'uses' => function(){

	}]);
	Route::get('/provider/{provider}/', 'ProfileController@redirectToProvider');
	Route::delete('/settings/{account}', ['as' => 'profile.destroy', 'uses' => 'ProfileController@destroyAccount']);
	Route::post('/settings', ['as' => 'profile.settings.ldap', 'uses' => 'ProfileController@addLdap']);
});