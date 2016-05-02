<?php namespace Modules\Reservation\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Auth;
use Pingpong\Modules\Facades\Module;
use Pingpong\Modules\Routing\Controller;
use App\Exceptions\Reservations\Collides;
use App\Exceptions\Reservations\BeforeNow;
use App\Http\Controllers\ApiBaseController;
use App\Exceptions\Reservations\MaxDuration;
use Modules\Reservation\Entities\Reservation;
use Modules\Experiments\Entities\PhysicalDevice;
use App\Exceptions\Reservations\TooManyForOneUser;
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
		try {
			$user = Auth::user()->user;
			$reservation = new ReservationService($user, $request);
			$message = $reservation->create();
			return $this->respondWithSuccess($message);

		} catch(ModelNotFoundException $e) {
			return $this->errorForbidden("Requested device for reservation was not found!");
		} catch(BeforeNow $e) {
			return $this->errorForbidden("Reservation cannot start before current time.");
		} catch(TooManyForOneUser $e) {
			$maxReservations = Module::get('Reservation')->settings('max_reservations_per_user');
			return $this->errorForbidden("You have reached the maximum number of reservations for today.");
		} catch(MaxDuration $e) {
			$maxDuration = Module::get('Reservation')->settings('max_reservation_time');
			return $this->errorForbidden("Maximum reservation time for a device is " . $maxDuration . " minutes.");
		}

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
			$user = Auth::user()->user;
			$reservation = new ReservationService($user, $request);
			$reservation->update($id);
			return $this->respondWithSuccess("Reservation updated");	
		} catch(ModelNotFoundException $e) {
			return $this->errorNotFound("Reservation not found!");
		} catch(BeforeNow $e) {
			return $this->errorForbidden("Reservation cannot start before current time.");
		} catch(TooManyForOneUser $e) {
			$maxReservations = Module::get('Reservation')->settings('max_reservations_per_user');
			return $this->errorForbidden("You have reached the maximum number of reservations for today.");
		} catch(MaxDuration $e) {
			$maxDuration = Module::get('Reservation')->settings('max_reservation_time');
			return $this->errorForbidden("Maximum reservation time for a device is " . $maxDuration . " minutes.");
		} catch(Collides $e) {
			return $this->errorForbidden("Reservations collide. Update failed.");
		}

	}

}
