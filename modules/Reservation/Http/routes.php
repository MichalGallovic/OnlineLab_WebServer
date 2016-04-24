<?php

Route::group(['prefix' => 'reservation', 'namespace' => 'Modules\Reservation\Http\Controllers'], function()
{
	Route::get('/calendar', ['as' => 'reservation.calendar','uses' => 'ReservationController@index']);
});

Route::group(['prefix' => 'api', 'namespace' => 'Modules\Reservation\Http\Controllers'], function() {
	Route::get('reservations', ['uses' => 'ApiController@reservations']);
	Route::post('reservations', ['uses' => 'ApiController@createReservation']);
	Route::put('reservations/{id}', ['uses' => 'ApiController@updateReservation']);
	Route::delete('reservations/{id}', ['uses' => 'ApiController@deleteReservation']);
});