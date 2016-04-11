<?php

Route::group(['prefix' => 'test', 'namespace' => 'Modules\Test\Http\Controllers'], function()
{
	Route::get('/test', ['as' => 'test.main', 'uses' => 'TestController@index']);
});