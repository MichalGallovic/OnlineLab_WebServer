<?php

namespace Modules\Experiments\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Experiments\Entities\Experiment;
use Modules\Experiments\Entities\ServerExperiment;



class AvailableExperimentTransformer extends TransformerAbstract
{

	protected $availableIncludes = [
		"commands",
		'output_arguments',
		"experiment_commands"
	];

	protected $experimentInstance;

	public function transform(Experiment $experiment)
	{
		$this->experimentInstance = ServerExperiment::hasExperiments()->where('experiment_id', $experiment->id)->first();

		return [
			"id"	=>	$experiment->id,
			"device" 		=>	$experiment->device->name,
			"software"	=>	$experiment->software->name
		];
	}

	public function includeCommands(Experiment $experiment)
	{
		return $this->item($this->experimentInstance->commands, new GeneralArrayTransformer);
	}

	public function includeOutputArguments(Experiment $experiment)
	{
		return $this->item($this->experimentInstance->output_arguments, new GeneralArrayTransformer);
	}

	public function includeExperimentCommands(Experiment $experiment)
	{
	return $this->item($this->experimentInstance->experiment_commands, new GeneralArrayTransformer);
	}
}