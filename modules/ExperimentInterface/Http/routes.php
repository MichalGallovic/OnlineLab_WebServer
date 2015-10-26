<?php

Route::group(['prefix' => 'experimentinterface', 'namespace' => 'Modules\ExperimentInterface\Http\Controllers'], function()
{
	Route::get('/', 'ExperimentInterfaceController@index');
});