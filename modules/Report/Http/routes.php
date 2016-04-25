<?php

Route::group(['prefix' => 'report', 'namespace' => 'Modules\Report\Http\Controllers'], function()
{
	Route::get('/', ['as' => 'report.index', 'uses' => 'ReportController@index']);
	Route::get('{id}', ['as' => 'report.show', 'uses' => 'ReportController@show']);
	Route::post('{id}', ['as' => 'report.update', 'uses' => 'ReportController@update']);
	Route::delete('{id}', ['as' => 'report.delete', 'uses' => 'ReportController@delete']);
});

Route::group(["prefix" => "api", "namespace" => 'Modules\Report\Http\Controllers'], function() {
	Route::post("report/{id}",['uses' => 'ApiController@update']);
});