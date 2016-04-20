<?php namespace Modules\Experiments\Http\Controllers;

use Illuminate\Support\Collection;
use App\Services\ExperimentService;
use Illuminate\Support\Facades\Log;
use Pingpong\Modules\Routing\Controller;
use App\Http\Controllers\ApiBaseController;
use App\Services\ExperimentInstanceService;
use Modules\Experiments\Entities\Experiment;
use Modules\Experiments\Entities\ServerExperiment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Experiments\Http\Requests\QueueExperimentRequest;
use Modules\Experiments\Http\Requests\ServerExperimentStatusRequest;
use Modules\Experiments\Transformers\AvailableExperimentTransformer;

class ApiController extends ApiBaseController {
	
	public function experiments()
	{
		$experiments = Experiment::available()->get();

		return $this->respondWithCollection($experiments, new AvailableExperimentTransformer);
		// $experiments = ServerExperiment::where('instances', '>', 0)->get();
		
		// $experimentVersions = $experiments->groupBy("experiment_id")->map(function($server_experiments) {
		// 	return $server_experiments->unique('commands');
		// })->collapse();

		// return $this->respondWithCollection($experimentVersions, new AvailableExperimentTransformer);
	}

	public function queue(QueueExperimentRequest $request, $id)
	{
		$experiment = Experiment::findOrFail($id);
		$experimentService = new ExperimentService($experiment, $request->input());
		$experimentService->queue();

		return $this->respondWithSuccess("Experiment queued!");
	}

	public function updateStatus(ServerExperimentStatusRequest $request)
	{
		$instanceName = $request->input("device_name");
		$device = $request->input("device");
		$software = $request->input("software");

		try {
			$instance = ServerExperiment::ofInstance($instanceName)->whereHas("experiment", function ($query) use ($device, $software){
				$query->whereHas('device', function($q) use ($device){
					$q->where("name", $device);
				})->whereHas("software", function($q) use ($software){
					$q->where("name", $software);
				});
			})->firstOrFail();

		} catch(ModelNotFoundException $e) {
			return $this->errorNotFound("Instance not found :/");
		}

		$instanceService = new ExperimentInstanceService($instance);
		$instanceService->updateStatus($request->input("status"));

		return $this->respondWithSuccess("Instance status updated");
	}
	
}