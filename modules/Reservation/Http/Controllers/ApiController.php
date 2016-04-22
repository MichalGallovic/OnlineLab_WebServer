<?php namespace Modules\Reservation\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Pingpong\Modules\Routing\Controller;
use App\Http\Controllers\ApiBaseController;
use Modules\Reservation\Entities\Reservation;
use Modules\Experiments\Entities\ServerExperiment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Reservation\Http\Requests\NewReservationRequest;
use Modules\Reservation\Transformers\ReservationTransformer;
use Modules\Reservation\Http\Requests\UpdateReservationRequest;

class ApiController extends ApiBaseController {

	public function reservations()
	{
		$reservations = Reservation::all();

		return $this->respondWithCollection($reservations, new ReservationTransformer);
	}

	public function createReservation(NewReservationRequest $request)
	{
		$instance = ServerExperiment::ofDevice($request->input('device'))
		->ofSoftware($request->input('software'))->ofInstance($request->input('instance'))->first();

		$reservation = Reservation::create([
				'user_id' => 1,
				'experiment_server_id' => $instance->id,
				'start' => $request->input('start'),
				'end'	=>	$request->input('end')
			]);

		return [
			"id"	=>	$reservation->id
		];
	}

	public function updateReservation(UpdateReservationRequest $request, $id)
	{
		try {
			$reservation = Reservation::findOrFail($id);
		} catch(ModelNotFoundException $e) {
			return $this->errorNotFound("Reservation not found!");
		}


		$instance = ServerExperiment::ofDevice($request->input('device'))
		->ofSoftware($request->input('software'))->ofInstance($request->input('instance'))->first();


		$start = new Carbon($request->input('start'));
		$end = new Carbon($request->input('end'));

		$collides = Reservation::where('experiment_server_id',$instance->id)
				->collidingWith($start, $end)->get();

		$collides = $collides->filter(function($item) use($reservation) {
			return $item->id != $reservation->id;
		});

		if($collides->count() == 0) {
			$reservation->update([
					"experiment_server_id" => $instance->id,
					"start"	=>	$start,
					"end"	=>	$end
				]);
			return $this->respondWithSuccess("Reservation updated");
		}

		return $this->errorForbidden("Reservations collide");
	}

}
