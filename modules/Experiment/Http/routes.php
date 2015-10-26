<?php

Route::group(['prefix' => 'experiment', 'namespace' => 'Modules\Experiment\Http\Controllers'], function()
{
	Route::get('/', 'ExperimentController@index');
});