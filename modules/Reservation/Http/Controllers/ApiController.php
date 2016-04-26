<?php namespace Modules\Reservation\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Pingpong\Modules\Routing\Controller;
use App\Http\Controllers\ApiBaseController;
use Modules\Reservation\Entities\Reservation;
use Modules\Experiments\Entities\PhysicalDevice;
use Modules\Experiments\Entities\ServerExperiment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Reservation\Http\Requests\NewReservationRequest;
use Modules\Reservation\Transformers\ReservationTransformer;
use Modules\Reservation\Http\Requests\UpdateReservationRequest;

class ApiController extends ApiBaseController {

	public function reservations()
	{
		if(Auth::user()->user->role == 'admin') {
			$reservations = Reservation::all();
		} else {
			$reservations = Reservation::whereHas('physicalDevice', function($q) {
				$q->whereNull('deleted_at');
			})->get();
		}


		return $this->respondWithCollection($reservations, new ReservationTransformer);
	}

	public function createReservation(NewReservationRequest $request)
	{
		$physicalDevice = PhysicalDevice::ofDevice($request->input('device'))
		->ofName($request->input('physical_device'))->first();

		$now = Carbon::now();
		$start = new Carbon($request->input('start'));

		if($now->gt($start)) {
			return $this->errorForbidden("Reservation cannot start before current time.");
		}


		$reservation = Reservation::firstOrCreate([
				'user_id' => Auth::user()->user->id,
				'physical_device_id' => $physicalDevice->id,
				'start' => $request->input('start'),
				'end'	=>	$request->input('end')
			]);

		$message = "Device " . $physicalDevice->device->name . ' ' . $physicalDevice->name . " reserved";
		$message .= "<br>";
		$message .= "<strong>" . $request->input('start') . "</strong>" . " - <strong>" . $request->input('end') . "</strong>";

		return $this->respondWithSuccess($message);
	}

	public function deleteReservation(Request $request, $id)
	{
		try {
			$reservation = Reservation::findOrFail($id);
		} catch(ModelNotFoundException $e) {
			return $this->errorNotFound("Reservation not found!");
		}

		if($reservation->user_id == Auth::user()->user->id || Auth::user()->user->role == 'admin') {
			$reservation->delete();
			return $this->respondWithSuccess("Reservation deleted!");
		} else {
			return $this->errorForbidden("You cannot delete this reservation");
		}
	}

	public function updateReservation(UpdateReservationRequest $request, $id)
	{
		try {
			$reservation = Reservation::findOrFail($id);
		} catch(ModelNotFoundException $e) {
			return $this->errorNotFound("Reservation not found!");
		}


		$physicalDevice = PhysicalDevice::ofDevice($request->input('device'))
		->ofName($request->input('physical_device'))->first();

		$now = Carbon::now();
		$start = new Carbon($request->input('start'));
		$end = new Carbon($request->input('end'));
		
		if($now->gt($start)) {
			return $this->errorForbidden("Reservation cannot start before current time.");
		}

		$collides = Reservation::collidingWith($start, $end)->get()->where('physical_device_id',$physicalDevice->id);


		$collides = $collides->filter(function($item) use($reservation) {
			return $item->id != $reservation->id;
		});

		if($collides->count() == 0) {
			$reservation->update([
					"physical_device_id" => $physicalDevice->id,
					"start"	=>	$start,
					"end"	=>	$end
				]);
			
			return $this->respondWithSuccess("Reservation updated");
		}

		return $this->errorForbidden("Reservations collide");
	}

}
