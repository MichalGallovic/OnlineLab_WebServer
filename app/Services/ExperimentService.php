<?php

namespace App\Services;

use Auth;
use App\User;
use Illuminate\Support\Arr;
use App\Services\ExperimentValidator;
use Modules\Experiments\Entities\Experiment;
use Illuminate\Foundation\Bus\DispatchesJobs;
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
		// $this->user = Auth::user()->user;
		$this->user = User::find(1);

		$instanceName = Arr::get($experimentInput,"instance");

		if($instanceName) {
		    $physicalDevice = PhysicalDevice::ofDevice($experimentInput['device'])->ofName($experimentInput['instance'])->first();
		} else {
		    $physicalDevice = PhysicalDevice::ofDevice($experimentInput['device'])->first();
		}

		$physicalExperiment = PhysicalExperiment::where('experiment_id', $this->experiment->id)->where('physical_device_id', $physicalDevice->id)->runnable()->firstOrFail();

		$validator = new ExperimentValidator($physicalExperiment->rules->toArray(), $experimentInput['input']);

		if($validator->fails()) {
		    throw new ValidationException($validator->errors());
		}
	}

	public function run()
	{
		$defaultDriver = app('queue')->getDefaultDriver();
		app('queue')->setDefaultDriver('sync');
		\Queue::push(new RunExperimentJob($this->user, $this->experiment, $this->experimentInput));
		app('queue')->setDefaultDriver($defaultDriver);
	}

	public function queue()
	{
		$this->dispatch(new RunExperimentJob($this->user, $this->experiment, $this->experimentInput));
	}


}