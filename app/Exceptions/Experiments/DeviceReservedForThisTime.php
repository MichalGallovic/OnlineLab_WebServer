<?php

namespace App\Exceptions\Experiments;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
* Thrown when device is reserved for requested
* time
*/
class DeviceReservedForThisTime extends \Exception
{
	protected $physicalDevices;
	protected $duration;

	public function __construct(Collection $physicalDevices, $duration)
	{
		$this->physicalDevices = $physicalDevices;
		$this->duration = $duration;
	}

	public function nextTrySeconds() {
		// From physical devices pick one, thats ending soonest among all
		$minSeconds = 1;

		$soonestEnd = $this->physicalDevices->map(function($physicalDevice) {
		    return $physicalDevice->reservations()->endAfterNow()->orderBy('end')->first();
		})->filter(function($reservation) {
		    return !is_null($reservation);
		})->min('end');
		// var_dump($soonestEnd);
		// get that ending time in seconds from now
		if(is_string($soonestEnd)) {
			var_dump($soonestEnd);
		    $soonestEnd = Carbon::now()->diffInSeconds(new Carbon($soonestEnd));
		    $minSeconds = ($soonestEnd > $minSeconds) ? $soonestEnd : $minSeconds;
		}

		return $minSeconds;
	}
}