<?php

namespace Modules\Experiments\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Experiments\Entities\ServerExperiment;



class AvailableExperimentTransformer extends TransformerAbstract
{

	protected $availableIncludes = [
		"commands",
		'output_arguments',
		"experiment_commands"
	];

	public function transform(ServerExperiment $server_experiment)
	{
		return [
			"id"	=>	$server_experiment->id,
			"device" 		=>	$server_experiment->experiment->device->name,
			"software"	=>	$server_experiment->experiment->software->name,
			"server"	=>	$server_experiment->server->name
		];
	}

	public function includeCommands(ServerExperiment $server_experiment)
	{
		return $this->item($server_experiment->commands, new GeneralArrayTransformer);
	}

	public function includeOutputArguments(ServerExperiment $server_experiment)
	{
		return $this->item($server_experiment->output_arguments, new GeneralArrayTransformer);
	}

	public function includeExperimentCommands(ServerExperiment $server_experiment)
	{
		return $this->item($server_experiment->experiment_commands, new GeneralArrayTransformer);
	}
}