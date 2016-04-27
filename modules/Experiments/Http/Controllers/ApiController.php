<?php namespace Modules\Experiments\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Services\ExperimentService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Pingpong\Modules\Routing\Controller;
use App\Http\Controllers\ApiBaseController;
use App\Services\ExperimentInstanceService;
use Modules\Experiments\Entities\Experiment;
use Modules\Experiments\Entities\PhysicalDevice;
use Modules\Experiments\Entities\ServerExperiment;
use Modules\Experiments\Entities\PhysicalExperiment;
use Modules\Experiments\Transformers\DeviceTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Experiments\Http\Requests\QueueExperimentRequest;
use Modules\Experiments\Http\Requests\ServerExperimentStatusRequest;
use Modules\Experiments\Transformers\AvailableExperimentTransformer;

class ApiController extends ApiBaseController {
	
	public function experiments(Request $request)
	{
		$user = Auth::user()->user;

		if($user->role == 'user') {
			$physicalExperiments = PhysicalExperiment::whereHas('server', function($q) {
				$q->available();
			})->whereHas('physicalDevice', function($q) {
				$q->online();
			});
		} else {
			$physicalExperiments = PhysicalExperiment::whereHas('server', function($q) {
				$q->availableForAdmin();
			})->whereHas('physicalDevice', function($q) {
				$q->online();
			});
		}

		if($request->input('type') == 'reservable') {
			$physicalExperiments->reservable();
		} else {
			$physicalExperiments->runnable();
		}

		$physicalExperiments = $physicalExperiments->get();

		return $this->respondWithCollection($physicalExperiments, new AvailableExperimentTransformer);
	}

	public function devices()
	{
		$user = Auth::user()->user;
		
		if($user->role == 'user') {
			$physicalDevices = PhysicalDevice::whereHas('server', function($q) {
				$q->available();
			})->online()->get();
		} else {
			$physicalDevices = PhysicalDevice::whereHas('server', function($q) {
				$q->availableForAdmin();
			})->online()->get();
		}

		

		return $this->respondWithCollection($physicalDevices, new DeviceTransformer);
	}

	public function queue(QueueExperimentRequest $request, $id)
	{
		$experiment = Experiment::findOrFail($id);
		$experimentService = new ExperimentService($experiment, $request->input());
		$experimentService->queue();

		return $this->respondWithSuccess("Experiment queued successfully!");
	}

	public function updateStatus(Request $request)
	{
		$physicalDeviceName = $request->input("device_name");
		$device = $request->input("device");
		$software = $request->input("software");

		try {
			$physicalDevice = Experiment::ofDevice($device)->ofSoftware($software)->first()->physicalDevices->where('name', $physicalDeviceName)->first();

		} catch(ModelNotFoundException $e) {
			return $this->errorNotFound("Instance not found :/");
		}

		$physicalDevice->status = $request->input('status');
		$physicalDevice->save();

		// $instanceService = new ExperimentInstanceService($instance);
		// $instanceService->updateStatus($request->input("status"));

		return $this->respondWithSuccess("Instance status updated");
	}
	
}