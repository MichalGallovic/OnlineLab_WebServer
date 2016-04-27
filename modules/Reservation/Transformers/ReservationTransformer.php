<?php

namespace Modules\Reservation\Transformers;

use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use Modules\Reservation\Entities\Reservation;


class ReservationTransformer extends TransformerAbstract
{


	public function transform(Reservation $reservation)
	{
		$title = $reservation->physicalDevice->device->name . " " . $reservation->physicalDevice->name;

		$currentUser = Auth::user()->user;

		return [
			"id"	=>	$reservation->id,
			"user"	=>	$reservation->user->name . " " . $reservation->user->surname,
			"user_id"	=>	$reservation->user->id,
			"title"	=>	$title,
			"start"		=>	$reservation->start,
			"end"		=>	$reservation->end,
			"device"	=>	[
				"name"	=>	$reservation->physicalDevice->device->name,
				"physical_device"	=>	$reservation->physicalDevice->name
			],
			"editable"	=>	$this->isEditable($reservation, $currentUser),
			"backgroundColor"	=>	$this->backgroundColor($reservation, $currentUser),
			"borderColor" => $this->borderColor($reservation, $currentUser),
			"available"	=>	(boolean ) !$reservation->physicalDevice->trashed()
		];
	}

	protected function isEditable($reservation, $user)
	{
		return ($user->id == $reservation->user->id || $user->role == 'admin') &&
		!$reservation->physicalDevice->trashed();
	}

	protected function backgroundColor($reservation, $user)
	{	
		if($reservation->physicalDevice->trashed()) {
			return '#d9534f';
		} else if($user->id == $reservation->user->id) {
			return "#5cb85c";
		} else {
			return "#5bc0de";
		}
	}

	protected function borderColor($reservation, $user)
	{
		if($reservation->physicalDevice->trashed()) {
			return '#d64843';
		} else if($user->id == $reservation->user->id) {
			return "#4eb14e";
		} else {
			return "#41b5d8";
		}
	}
}