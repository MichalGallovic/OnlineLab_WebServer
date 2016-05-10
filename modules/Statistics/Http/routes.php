<?php

Route::group(['prefix' => 'statistics', 'namespace' => 'Modules\Statistics\Http\Controllers'], function()
{
	Route::get('/', ['as' => 'statistics.index', 'uses' => 'StatisticsController@index']);
});