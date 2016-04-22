<?php namespace Modules\Reservation\Http\Controllers;

use Carbon\Carbon;
use Pingpong\Modules\Routing\Controller;
use App\Http\Controllers\ApiBaseController;
use Modules\Reservation\Entities\Reservation;
use Modules\Experiments\Entities\ServerExperiment;
use Modules\Reservation\Http\Requests\NewReservationRequest;
use Modules\Reservation\Transformers\ReservationTransformer;

class ApiController extends ApiBaseController {

	public function reservations()
	{
		$reservations = Reservation::all();

		return $this->respondWithCollection($reservations, new ReservationTransformer);
	}

	public function createReservation(NewReservationRequest $request)
	{
		$deviceName = $request->input('device');
		$softwareName = $request->input('software');
		$instance = ServerExperiment::whereHas('experiment', function($query) use($deviceName, $softwareName) {
			$query->whereHas('device', function($q) use($deviceName, $softwareName) {
				$q->where('name',$deviceName);
			});
			$query->whereHas('software', function($q) use($deviceName, $softwareName) {
				$q->where('name',$softwareName);
			});
		})->where('device_name',$request->input('instance'))->first();

		$reservation = Reservation::create([
				'user_id' => 1,
				'experiment_server_id' => $instance->id,
				'start' => $request->input('start'),
				'end'	=>	$request->input('end')
			]);

		return ["Ok"];
	}

}
