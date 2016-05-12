<?php

Route::group(['namespace' => 'Modules\Controller\Http\Controllers', 'middleware' => 'auth'], function() {
	Route::resource('controller', 'ControllerController', ['except' => ['create']]);
	Route::group(['prefix' => 'controller'], function() {
		Route::get('schema/data/', ['as' => 'controller.schema.data', 'uses' => 'SchemaController@getData']);
		Route::get('schema/image/{schemaId}', ['as' => 'controller.schema.image', 'uses' => 'SchemaController@getImage']);
		Route::get('schema/image/', ['as' => 'controller.schema.image']);
		Route::resource('schema', 'SchemaController', ['except' => ['create']]);
		Route::get('/create/{enviroment}', ['as' => 'controller.create', 'uses' => 'ControllerController@create']);
		Route::post('upload', ['as' => 'controller.upload', 'uses' => 'ControllerController@upload']);

		Route::PATCH('/{controllerId}/approve', ['as' => 'controller.approve', 'uses' => 'ControllerController@approve']);
		Route::get('file/{controllerId}', ['middleware' => 'App\Http\Middleware\ControllerMiddleware', 'as' => 'controller.download', 'uses' => function($controllerId)
		{
			$regulator = \Modules\Controller\Entities\Regulator::find($controllerId);
			return Response::download($regulator->getFilePath());
		}]);


	});


});