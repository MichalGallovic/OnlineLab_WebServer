<?php

Route::group(['prefix' => 'lang', 'namespace' => 'Modules\Localization\Http\Controllers'], function()
{
	Route::get('{lang}', ['as'=>'lang.switch','uses'=>'LocalizationController@switchLang']);
});