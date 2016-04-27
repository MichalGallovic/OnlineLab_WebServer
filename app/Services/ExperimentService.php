<?php

namespace App\Services;

use Auth;
use Modules\Experiments\Entities\Experiment;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Modules\Experiments\Jobs\RunExperimentJob;
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