<?php

Route::group(['prefix' => 'reservation', 'namespace' => 'Modules\Reservation\Http\Controllers'], function()
{
	Route::get('/', 'ReservationController@index');
});