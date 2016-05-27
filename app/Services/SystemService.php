<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Repositories\ServersRepository;
use Modules\Experiments\Entities\Device;
use Modules\Experiments\Entities\Server;
use App\Classes\ApplicationServer\System;
use Modules\Experiments\Entities\Software;
use App\Repositories\ExperimentsRepository;
use Modules\Experiments\Entities\Experiment;
use Modules\Experiments\Entities\PhysicalDevice;
use Modules\Experiments\Entities\ServerExperiment;
use Modules\Experiments\Entities\PhysicalExperiment;

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

	public function syncWithServers()
	{
		$this->updateAvailability();
		$this->syncExperiments();
		$this->syncPhysicalDevices();
		$this->syncPhysicalExperiments();
	}

	public function updateAvailability()
	{
		$this->servers = Server::all();
		$this->system = new System($this->servers->lists('ip')->toArray());

		$servers = $this->system->getServers();
		foreach ($servers as $server) {
			$serverModel = Server::withTrashed()->where('ip',$server->getIp())->first();
			if($serverModel->trashed() && $server->getReachable() && $server->getDatabaseAvailable()) {
				$serverModel->restore();
			}
			$serverModel->reachable = $server->getReachable();
			$serverModel->database = $server->getDatabaseAvailable();
			$serverModel->save();
		}
	}

	public function syncExperiments()
	{
		// Syncing devics, softwares and experiments
		$rtExperiments = $this->system->experiments();


		foreach($rtExperiments as $rtExperiment) {
			$device = Device::firstOrCreate([
					"name"	=>	$rtExperiment["device"]
				]);
			$software = Software::firstOrCreate([
					"name"	=>	$rtExperiment["software"]
				]);

			$experiment = Experiment::whereHas('device', function($q) use ($rtExperiment) {
				$q->where('name',$rtExperiment['device']);
			})->whereHas('software', function($q) use ($rtExperiment) {
				$q->where('name',$rtExperiment['software']);
			})->firstOrCreate([
				"device_id"	=>	$device->id,
				"software_id"	=>	$software->id,
			]);
		}
	}

	public function syncPhysicalDevices()
	{
		// Syncing physical devices
		$rtPhysicalDevices = $this->system->devices();

		$availablePhysicalDevices = new Collection();
		foreach($rtPhysicalDevices as $rtDevice) {
			$physicalDevice = PhysicalDevice::withTrashed()->whereHas('device', function($q) use ($rtDevice) {
				$q->where('name',$rtDevice['name']);
			})->where('name',$rtDevice['device_name'])->firstOrCreate([
				"device_id"	=>	Device::where('name',$rtDevice['name'])->first()->id,
				"server_id"	=>	Server::where('ip',$rtDevice['ip'])->first()->id,
				"name"		=>	$rtDevice['device_name']
			]);

			if($physicalDevice->trashed()) {
				$physicalDevice->restore();
			}

			$physicalDevice->status = $rtDevice['status'];
			$physicalDevice->save();
			$availablePhysicalDevices->push($physicalDevice);
		}
		PhysicalDevice::whereNotIn('id',$availablePhysicalDevices->lists('id')->toArray())->get()->each(function($physicalDevice) {
			$physicalDevice->status = "offline";
			$physicalDevice->save();
			$physicalDevice->delete();
		});
	}

	public function syncPhysicalExperiments()
	{
		// Syncing physical experiments
		$rtPhysicalExperiments = $this->system->physicalExperiments();
		$availablePhysicalExperiments = new Collection();
		foreach ($rtPhysicalExperiments as $rtPhysicalExperiment) {
			$experiment = Experiment::whereHas('device', function($q) use ($rtPhysicalExperiment) {
				$q->where('name',$rtPhysicalExperiment['device']);
			})->whereHas('software', function($q) use ($rtPhysicalExperiment) {
				$q->where('name',$rtPhysicalExperiment['software']);
			})->first();

			$server = Server::where('ip', $rtPhysicalExperiment['ip'])->first();

			$physicalDevice = PhysicalDevice::where('name', $rtPhysicalExperiment['device_name'])
			->where('server_id', $server->id)->first();

			$physicalExperiment = PhysicalExperiment::withTrashed()->where('experiment_id',$experiment->id)
			->where('server_id',$server->id)->where('physical_device_id', $physicalDevice->id)->firstOrCreate([
					"server_id"	=>	$server->id,
					"experiment_id"	=>	$experiment->id,
					"physical_device_id"	=>	$physicalDevice->id
				]);

			if($physicalExperiment->trashed()) {
				$physicalExperiment->restore();
			}

			$experimentCommands = Arr::get($rtPhysicalExperiment,"experiment_commands.data");
			$commands = Arr::get($rtPhysicalExperiment,"input_arguments.data");

			$physicalExperiment->commands = empty($commands) ? null : $commands;
			$physicalExperiment->output_arguments = Arr::get($rtPhysicalExperiment,"output_arguments.data");
			$physicalExperiment->experiment_commands = empty($experimentCommands) ? null : $experimentCommands;
			$physicalExperiment->save();

			$availablePhysicalExperiments->push($physicalExperiment);
		}

		PhysicalExperiment::whereNotIn('id',$availablePhysicalExperiments->lists('id')->toArray())->get()->each(function($physicalExperiment) {
			$physicalExperiment->delete();
		});
	}
}