<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Pingpong\Modules\Facades\Module;
use Illuminate\Support\Facades\Request;
use App\Exceptions\Reservations\Collides;
use App\Exceptions\Reservations\MaxDuration;
use Modules\Reservation\Entities\Reservation;
use Modules\Experiments\Entities\PhysicalDevice;
use App\Exceptions\Reservations\TooManyForOneUser;

/**
* Reservation service
*/
class ReservationService
{
	/**
	 * User reference
	 * @var App\User
	 */
	protected $user;

	/**
	 * Request 
	 * @var Illuminate\Http\Request
	 */
	protected $request;

	public function __construct($user, $request)
	{
		$this->user = $user;
		$this->request = $request;
	}

	public function create()
	{
		$physicalDevice = PhysicalDevice::ofDevice($this->request->input('device'))
		->ofName($this->request->input('physical_device'))->firstOrFail();

		$this->checkValidity($physicalDevice);

		$reservation = Reservation::firstOrCreate([
				'user_id' => $this->user->id,
				'physical_device_id' => $physicalDevice->id,
				'start' => $this->request->input('start'),
				'end'	=>	$this->request->input('end')
			]);

		$message = "Device " . $physicalDevice->device->name . ' ' . $physicalDevice->name . " reserved";
		$message .= "<br>";
		$message .= "<strong>" . $this->request->input('start') . "</strong>" . " - <strong>" . $this->request->input('end') . "</strong>";

		return $message;
	}

	public function update($id)
	{
		$reservation = Reservation::findOrFail($id);

		$physicalDevice = PhysicalDevice::ofDevice($this->request->input('device'))
		->ofName($this->request->input('physical_device'))->first();

		$this->checkValidity($physicalDevice, $reservation);

		$reservation->update([
				"physical_device_id" => $physicalDevice->id,
				'start' => $this->request->input('start'),
				'end'	=>	$this->request->input('end')
			]);
	}

	protected function checkCollisionsFor($start, $end, PhysicalDevice $physicalDevice, Reservation $reservation = null)
	{
		$collides = Reservation::collidingWith($start, $end)->get()->where('physical_device_id',$physicalDevice->id);

		if(!is_null($reservation)) {
			$collides = $collides->filter(function($item) use($reservation) {
				return $item->id != $reservation->id;
			});
		}

		if($collides->count() > 0) {
			throw new Collides;
		}
	}

	protected function isAfterNow($start)
	{
		$now = Carbon::now();

		if($now->gt($start)) {
			throw new BeforeNow;
		}
	}

	protected function lastsLessThanLimit($start, $end)
	{
		$start = new Carbon($this->request->input('start'));
		$end = new Carbon($this->request->input('end'));
		$maxDuration = Module::get('Reservation')->settings('max_reservation_time');

		if($start->diffInMinutes($end) > $maxDuration) {
			throw new MaxDuration;
		}
	}

	protected function lessReservationsThanLimit($start)
	{
		$maxReservations = Module::get('Reservation')->settings('max_reservations_per_user');

		if($this->user->reservations()->forDay($start)->count() + 1 > $maxReservations) {
			throw new TooManyForOneUser;
		}
	}

	protected function checkValidity(PhysicalDevice $physicalDevice, Reservation $reservation = null)
	{
		$start = new Carbon($this->request->input('start'));
		$end = new Carbon($this->request->input('end'));
		
		$this->checkCollisionsFor($start, $end, $physicalDevice, $reservation);
		
		if($this->user->role == 'admin') return;

		$this->isAfterNow($start);
		$this->lastsLessThanLimit($start, $end);
		$this->lessReservationsThanLimit($start);
	}

	
}