<?php

namespace App\Services;

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

	public function __construct(Experiment $experiment, array $experimentInput)
	{
		$this->experiment = $experiment;
		$this->experimentInput = $experimentInput;
	}

	public function run()
	{

	}

	public function queue()
	{
		$this->dispatch(new RunExperimentJob($this->experiment, $this->experimentInput));
	}


}