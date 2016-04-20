<?php

Route::group(['prefix' => 'report', 'namespace' => 'Modules\Report\Http\Controllers'], function()
{
	Route::get('/', ['as' => 'report.index', 'uses' => 'ReportController@index']);
});

Route::group(["prefix" => "api", "namespace" => 'Modules\Report\Http\Controllers'], function() {
	Route::post("report/{id}",['uses' => 'ApiController@update']);
});