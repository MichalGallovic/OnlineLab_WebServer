<?php

Route::group(['prefix' => 'reservation', 'namespace' => 'Modules\Reservation\Http\Controllers'], function()
{
	Route::get('/calendar', ['as' => 'reservation.calendar','uses' => 'ReservationController@index']);
});