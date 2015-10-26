<?php

Route::group(['prefix' => 'livechart', 'namespace' => 'Modules\Livechart\Http\Controllers'], function()
{
	Route::get('/', 'LivechartController@index');
});