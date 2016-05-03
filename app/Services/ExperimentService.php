<?php

namespace App\Services;

use Auth;
use App\User;
use Illuminate\Support\Arr;
use App\Services\ExperimentRunner;
use App\Services\ExperimentValidator;
use Modules\Experiments\Entities\Experiment;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Exceptions\Experiments\DeviceNotReady;
use Modules\Experiments\Jobs\RunExperimentJob;
use Modules\Experiments\Entities\PhysicalDevice;
use Modules\Experiments\Entities\PhysicalExperiment;
use Illuminate\Contracts\Validation\ValidationException;
/**
* Experiment Service
*/
class ExperimentService
{
	use DispatchesJobs;
	
	protected $experiment;
	protected $experimentInput;

	public function __construct(Experiment $experiment, array $experimentInput = [])
	{
		$this->experiment = $experiment;
		$this->experimentInput = $experimentInput;
		$this->user = Auth::user()->user;

		$this->isInputValid();
	}

	public function run()
	{
		try {
		    $runner = new ExperimentRunner(
		        $this->user, $this->experiment, $this->experimentInput
		    );
		    $runner->run();
		} catch(DeviceNotReady $e) {
		    // The experiment should be ready, we should tell somebody
		}
	}

	public function queue()
	{
		$this->dispatch(new RunExperimentJob($this->user, $this->experiment, $this->experimentInput));
	}

	protected function isInputValid()
	{
		$instanceName = Arr::get($this->experimentInput,"instance");

		if($instanceName) {
		    $physicalDevice = PhysicalDevice::ofDevice($this->experimentInput['device'])->ofName($instanceName)->first();
		} else {
		    $physicalDevice = PhysicalDevice::ofDevice($this->experimentInput['device'])->first();
		}

		$physicalExperiment = PhysicalExperiment::where('experiment_id', $this->experiment->id)
		->where('physical_device_id', $physicalDevice->id)->runnable()->firstOrFail();

		$validator = new ExperimentValidator($physicalExperiment->rules->toArray(), $this->experimentInput['input']);

		if($validator->fails()) {
		    throw new ValidationException($validator->errors());
		}
	}


}