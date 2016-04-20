<?php

namespace App\Services;

use Modules\Experiments\Entities\ServerExperiment;

/**
* Experiment instance service
*/
class ExperimentInstanceService
{
	protected $instance;

	public function __construct(ServerExperiment $instance)
	{
		$this->instance = $instance;
	}

	public function updateStatus($status)
	{
		if(in_array($status, ["offline","ready","experimenting"]))
		{
			$this->instance->status = $status;
			$this->instance->save();
		}
	}
}