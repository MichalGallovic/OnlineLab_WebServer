<?php

Route::group(['namespace' => 'Modules\Controller\Http\Controllers'], function() {
	Route::resource('controller', 'ControllerController', ['except' => ['create']]);
	Route::group(['prefix' => 'controller'], function() {
		Route::get('/create/{enviroment}', ['as' => 'controller.create', 'uses' => 'ControllerController@create']);
		Route::post('upload', ['as' => 'controller.upload', 'uses' => 'ControllerController@upload']);
		Route::PATCH('/{controllerId}/approve', ['as' => 'controller.approve', 'uses' => 'ControllerController@approve']);
		Route::get('file/{controllerId}', ['middleware' => 'App\Http\Middleware\ControllerMiddleware', 'as' => 'controller.download', 'uses' => function($controllerId)
		{
			$regulator = \Modules\Controller\Entities\Regulator::find($controllerId);
			$filepath = storage_path() . '/user_uploads/'.$regulator->user->id.'/regulators/' . $regulator->filename;
			return Response::download($filepath);
		}]);
	});
});

//dump autoload