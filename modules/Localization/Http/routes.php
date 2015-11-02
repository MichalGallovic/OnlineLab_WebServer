<?php

Route::group(['prefix' => 'language', 'namespace' => 'Modules\Localization\Http\Controllers'], function()
{
	Route::get('settings', ['as'=>'lang.settings', 'uses'=>'LocalizationController@getSettings']);
	Route::get('{lang}', ['as'=>'lang.switch','uses'=>'LocalizationController@switchLang']);
});