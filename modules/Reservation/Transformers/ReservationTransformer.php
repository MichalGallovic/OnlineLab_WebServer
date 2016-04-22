<?php

namespace Modules\Reservation\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Reservation\Entities\Reservation;


class ReservationTransformer extends TransformerAbstract
{


	public function transform(Reservation $reservation)
	{
		$title = $reservation->experimentInstance->experiment->device->name . " "
		.$reservation->experimentInstance->experiment->software->name . " "
		.$reservation->experimentInstance->device_name;

		return [
			"id"	=>	$reservation->id,
			"user"	=>	$reservation->user->name . " " . $reservation->user->surname,
			"title"	=>	$title,
			"device"	=>	$reservation->experimentInstance->experiment->device->name,
			"software"	=>	$reservation->experimentInstance->experiment->software->name,
			"start"		=>	$reservation->start,
			"end"		=>	$reservation->end,
			"instance"	=>	$reservation->experimentInstance->device_name
		];
	}
}