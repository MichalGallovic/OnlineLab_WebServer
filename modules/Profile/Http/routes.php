<?php

Route::group(['prefix' => 'profile', 'namespace' => 'Modules\Profile\Http\Controllers'], function()
{
	Route::get('/settings', ['as' => 'profile.settings', 'uses' => 'ProfileController@getSettings']);
});