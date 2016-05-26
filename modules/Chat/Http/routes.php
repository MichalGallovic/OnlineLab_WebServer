<?php

Route::group(['prefix' => 'chat', 'namespace' => 'Modules\Chat\Http\Controllers', 'middleware' => 'auth'], function()
{
	Route::get('/', ['as' => 'chat.index', 'uses' =>'ChatController@index']);
	Route::get('/findUsers', ['as' => 'chat.findUsers', 'uses' => 'ChatController@findUsers']);
	Route::get('/{chatroom}', ['middleware' => 'App\Http\Middleware\ChatMiddleware', 'as' => 'chat.chatroom', 'uses' => 'ChatController@chatroom']);

	Route::post('/video', ['as' => 'chat.new.video', 'uses' => 'ChatController@createVideo']);
	Route::get('/video/{id}', ['as' => 'chat.video', 'uses' => 'ChatController@joinVideo']);
	Route::post('/addUser', ['as' => 'chat.addUsers', 'uses' => 'ChatController@addUser']);
	Route::post('/', ['as' => 'chat.new.chatroom', 'uses' => 'ChatController@storeChatroom']);

});
