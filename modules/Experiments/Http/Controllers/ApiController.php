<?php namespace Modules\Experiments\Http\Controllers;

use Illuminate\Support\Collection;
use App\Services\ExperimentService;
use Pingpong\Modules\Routing\Controller;
use App\Http\Controllers\ApiBaseController;
use Modules\Experiments\Entities\Experiment;
use Modules\Experiments\Entities\ServerExperiment;
use Modules\Experiments\Http\Requests\QueueExperimentRequest;
use Modules\Experiments\Transformers\AvailableExperimentTransformer;

class ApiController extends ApiBaseController {
	
	public function experiments()
	{
		$experiments = ServerExperiment::where('instances', '>', 0)->get();
		
		$experimentVersions = $experiments->groupBy("experiment_id")->map(function($server_experiments) {
			return $server_experiments->unique('commands');
		})->collapse();

		return $this->respondWithCollection($experimentVersions, new AvailableExperimentTransformer);
	}

	public function queue(QueueExperimentRequest $request, $id)
	{
		$experiment = ServerExperiment::findOrFail($id);
		$experimentService = new ExperimentService($experiment, $request->input());
		$experimentService->queue();

		return $this->respondWithSuccess("Experiment queued!");
	}
	
}