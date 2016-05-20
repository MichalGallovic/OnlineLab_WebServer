<?php

Route::group(['prefix' => 'forum', 'namespace' => 'Modules\Forum\Http\Controllers', 'middleware' => 'auth'], function()
{
	Route::get('/', ['as' => 'forum.index', 'uses' => 'ForumController@index']);
	Route::get('/category/{id}', array('uses' => 'ForumController@category', 'as' => 'forum.category'));
	Route::get('/thread/{id}', array('uses' => 'ForumController@thread', 'as' => 'forum.thread'));
	Route::get('/thread/new/{id}', array('uses' => 'ForumController@newThread', 'as' => 'forum.new.thread'));

	//Route::group(array('before' => 'admin'), function(){
	//Todo Filter middleware isAdmin
		Route::group(array('before' => 'csrv'), function(){
			Route::post('/group', array('uses' => 'ForumController@storeGroup', 'as' => 'forum.store.group'));
			Route::post('/category/{id}', array('uses' => 'ForumController@storeCategory', 'as' => 'forum.store.category'));
			Route::delete('/group/{id}', array('uses' => 'ForumController@deleteGroup', 'as' => 'forum.delete.group'));
			Route::delete('/category/{id}', array('uses' => 'ForumController@deleteCategory', 'as' => 'forum.delete.category'));
			Route::delete('/thread/{id}', array('uses' => 'ForumController@deleteThread', 'as' => 'forum.delete.thread'));
			Route::delete('/comment/{id}', array('uses' => 'ForumController@deleteComment', 'as' => 'forum.delete.comment'));
		});
	//});

	//Todo Filter auth
	//Route::get('/thread/{id}', array('uses' => 'ForumController@newThread', 'as' => 'forum.store.thread'));



	Route::group(array('before' => 'csrv'), function(){
		Route::post('/thread/{id}', array('uses' => 'ForumController@storeThread', 'as' => 'forum.store.thread'));
		Route::post('/comment/{id}', array('uses' => 'ForumController@storeComment', 'as' => 'forum.store.comment'));
	});
});

