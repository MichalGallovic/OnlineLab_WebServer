<?php

Route::group(['prefix' => 'controller', 'namespace' => 'Modules\Controller\Http\Controllers'], function()
{
	Route::get('/', ['as' => 'controller.index', 'uses' => 'ControllerController@index']);
});