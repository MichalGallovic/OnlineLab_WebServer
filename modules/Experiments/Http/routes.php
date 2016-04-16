<?php

Route::group(['prefix' => 'experiments', 'namespace' => 'Modules\Experiments\Http\Controllers'], function()
{
	Route::get('/', ['as'	=>	"experiments.index", "uses"	=>	'ExperimentsController@index']);
	Route::get('refresh', ['as' => 'experiments.refresh', 'uses' => 'ExperimentsController@refresh']);
	Route::get('server/add',['as' => 'experiments.server.create', "uses" => 'ExperimentsController@createServer']);
	Route::post('server/add',['as' => 'experiments.server.store', "uses" => 'ExperimentsController@storeServer']);
	Route::get('server/sync', ['as' => 'experiments.server.sync', "uses" => 'ExperimentsController@sync']);
});