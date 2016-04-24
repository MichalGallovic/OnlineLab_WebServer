<?php

namespace Modules\Experiments\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Experiments\Entities\Experiment;
use Modules\Experiments\Entities\PhysicalExperiment;



class AvailableExperimentTransformer extends TransformerAbstract
{

	protected $availableIncludes = [
		"commands",
		'output_arguments',
		"experiment_commands"
	];

	protected $experimentInstance;

	public function transform(PhysicalExperiment $physicalExperiment)
	{
		$experiment = $physicalExperiment->experiment;

		return [
			"experiment_id"	=>	$experiment->id,
			"device" 		=>	$experiment->device->name,
			"software"	=>	$experiment->software->name,
			"physical_device"	=>	$physicalExperiment->physicalDevice->name,
			"production"	=>	(boolean) $physicalExperiment->server->production
		];
	}

	public function includeCommands(PhysicalExperiment $physicalExperiment)
	{
		return $this->item($physicalExperiment->commands, new GeneralArrayTransformer);
	}

	public function includeOutputArguments(PhysicalExperiment $physicalExperiment)
	{
		return $this->item($physicalExperiment->output_arguments, new GeneralArrayTransformer);
	}

	public function includeExperimentCommands(PhysicalExperiment $physicalExperiment)
	{
	return $this->item($physicalExperiment->experiment_commands, new GeneralArrayTransformer);
	}
}