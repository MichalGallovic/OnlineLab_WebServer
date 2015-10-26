<?php

Route::group(['prefix' => 'equipments', 'namespace' => 'Modules\Equipments\Http\Controllers'], function()
{
	Route::get('/', 'EquipmentsController@index');
});