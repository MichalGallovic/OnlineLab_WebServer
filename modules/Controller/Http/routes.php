<?php

Route::group(['prefix' => 'controller', 'namespace' => 'Modules\Controller\Http\Controllers'], function()
{
	Route::get('/', 'ControllerController@index');
});