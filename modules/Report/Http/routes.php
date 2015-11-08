<?php

Route::group(['prefix' => 'report', 'namespace' => 'Modules\Report\Http\Controllers'], function()
{
	Route::get('/', ['as' => 'report.index', 'uses' => 'ReportController@index']);
});