<?php

namespace App\Services;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Modules\Experiments\Jobs\RunExperimentJob;
use Modules\Experiments\Entities\ServerExperiment;
/**
* Experiment Service
*/
class ExperimentService
{
	use DispatchesJobs;
	
	protected $experimentInstance;
	protected $experimentInput;

	public function __construct(ServerExperiment $experimentInstance, array $experimentInput)
	{
		$this->experimentInstance = $experimentInstance;
		$this->experimentInput = $experimentInput;
	}

	public function run()
	{

	}

	public function queue()
	{
		$this->dispatch(new RunExperimentJob($this->experimentInstance->server->ip, $this->experimentInput));
	}


}