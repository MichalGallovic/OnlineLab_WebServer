<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use App\Repositories\ServersRepository;
use Modules\Experiments\Entities\Device;
use Modules\Experiments\Entities\Server;
use App\Classes\ApplicationServer\System;
use Modules\Experiments\Entities\Software;
use App\Repositories\ExperimentsRepository;
use Modules\Experiments\Entities\Experiment;
use Modules\Experiments\Entities\ServerExperiment;

/**
* System service
*/
class SystemService
{
	protected $system;
	protected $servers;

	public function __construct()
	{
		$this->servers = Server::all();
		$this->system = new System($this->servers->lists('ip')->toArray());
	}

	public function updateAvailability()
	{
		$servers = $this->system->getServers();
		foreach ($servers as $server) {
			$serverModel = Server::where('ip',$server->getIp())->first();
			$serverModel->available = $server->getAvailability();
			$serverModel->reachable = $server->getReachable();
			$serverModel->database = $server->getDatabaseAvailable();
			$serverModel->queue = $server->getQueueAvailable();
			$serverModel->redis = $server->getRedisAvailable();
			$serverModel->save();
		}
	}

	public function syncWithServers()
	{
		$appServerExperiments = $this->system->uniqueExperiments();

		$webServerExperiments = Experiment::all();

		$newExperiments = new Collection();
		$webServerToActivate = new Collection();

		foreach ($appServerExperiments as $appServerExperiment) {
			$isNew = true;

			foreach ($webServerExperiments as $webServerExperiment) {
				if($webServerExperiment->device->name == $appServerExperiment["device"] &&
					$webServerExperiment->software->name == $appServerExperiment["software"]) {
					$webServerToActivate->push($webServerExperiment);
					$isNew = false;
					break;
				}
			}
			if($isNew) {
				$newExperiments->push($appServerExperiment);
			}
		}

		$webServerToDeactivate = $webServerExperiments->diff($webServerToActivate);

		foreach ($webServerToDeactivate as $experiment) {
			$experiment->available = false;
			$experiment->save();
		}

		foreach ($webServerToActivate as $experiment) {
			$experiment->available = true;
			$experiment->save();
		}

		foreach ($newExperiments as $experiment) {
			$device = Device::firstOrCreate([
					"name"	=>	$experiment["device"]
				]);
			$software = Software::firstOrCreate([
					"name"	=>	$experiment["software"]
				]);
			$experiment = new Experiment;
			$experiment->device()->associate($device);
			$experiment->software()->associate($software);
			$experiment->available = true;
			$experiment->save();
		}



		$experiments = Experiment::all();
		$experimentInstances = $this->system->experiments()->groupBy('ip');


		$availableExperimentInstances = new Collection();

		foreach ($experimentInstances as $ip => $serverExperiments) {
			foreach ($serverExperiments as $experiment) {

				$serverIp = str_replace("/", "", $ip);
				$server = $this->servers->where('ip',$serverIp)->first();
				
				$webServerExperiment = Experiment::whereHas("device", function($q) use ($experiment) {
					$q->where('name',$experiment["device"]);
				})->whereHas("software", function($q) use ($experiment) {
					$q->where('name',$experiment["software"]);
				})->first();
				


				$server_experiment = ServerExperiment::where("experiment_id",$webServerExperiment->id)->firstOrCreate([
						"server_id"	=>	$server->id,
						"experiment_id"	=>	$webServerExperiment->id
					]);

				$server_experiment->commands = Arr::get($experiment,"input_arguments.data");
				$server_experiment->output_arguments = Arr::get($experiment,"output_arguments.data");
				$server_experiment->experiment_commands = Arr::get($experiment,"experiment_commands.data");
				$server_experiment->save();

				$availableExperimentInstances->push($server_experiment);
			}
		}

		$availableExperimentInstances->groupBy('id')->each(function($groupOfInstances, $key) {
			$server_experiment = $groupOfInstances->first();
			$server_experiment->instances = $groupOfInstances->count();
			$server_experiment->free_instances = $groupOfInstances->count();
			$server_experiment->save();
		});
	
		ServerExperiment::all()->diff($availableExperimentInstances)->each(function($server_experiment) {
			$server_experiment->instances = 0;
			$server_experiment->save();
		});		


		$this->updateAvailability();
	}
}