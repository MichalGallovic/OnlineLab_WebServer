<?php

namespace Modules\Reservation\Transformers;

use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use Modules\Reservation\Entities\Reservation;


class ReservationTransformer extends TransformerAbstract
{


	public function transform(Reservation $reservation)
	{
		$title = $reservation->experimentInstance->experiment->device->name . " "
		.$reservation->experimentInstance->experiment->software->name . " "
		.$reservation->experimentInstance->device_name;

		$currentUser = Auth::user()->user;

		return [
			"id"	=>	$reservation->id,
			"user"	=>	$reservation->user->name . " " . $reservation->user->surname,
			"user_id"	=>	$reservation->user->id,
			"title"	=>	$title,
			"device"	=>	$reservation->experimentInstance->experiment->device->name,
			"software"	=>	$reservation->experimentInstance->experiment->software->name,
			"start"		=>	$reservation->start,
			"end"		=>	$reservation->end,
			"instance"	=>	$reservation->experimentInstance->device_name,
			"editable"	=>	($currentUser->id == $reservation->user->id) ? true : false,
			"backgroundColor"	=>	($currentUser->id == $reservation->user->id) ? "#5cb85c" : "#5bc0de",
			"borderColor" => ($currentUser->id == $reservation->user->id) ? "#4eb14e" : "#41b5d8"
		];
	}
}