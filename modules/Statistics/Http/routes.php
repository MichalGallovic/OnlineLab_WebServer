<?php

Route::group(['prefix' => 'statistics', 'namespace' => 'Modules\Statistics\Http\Controllers', 'middlware' => 'auth'], function()
{
	Route::get('/', ['as' => 'statistics.index', 'uses' => 'StatisticsController@index']);
});