<?php

Route::group(['prefix' => 'experiments', 'namespace' => 'Modules\Experiments\Http\Controllers'], function()
{
	Route::get('/', ['as'	=>	"experiments.index", "uses"	=>	'ExperimentsController@index']);
	Route::get('refresh', ['as' => 'experiments.refresh', 'uses' => 'ExperimentsController@refresh']);

	Route::get('servers/add',['as' => 'servers.create', "uses" => 'ServersController@create']);
	Route::post('servers/add',['as' => 'servers.store', "uses" => 'ServersController@store']);
	Route::get('servers/{id}/edit',['as' => 'servers.edit', "uses" => 'ServersController@edit']);
	Route::patch('servers/{id}/edit',['as' => 'servers.update', "uses" => 'ServersController@update']);
	Route::get('servers/sync', ['as' => 'servers.sync', "uses" => 'ServersController@sync']);
	Route::get('servers/status/refresh', ['as' => 'servers.refreshStatus', "uses" => 'ServersController@refresh']);
});