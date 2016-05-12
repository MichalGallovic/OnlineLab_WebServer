<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use App\Classes\ApplicationServer\Server;
use Modules\Experiments\Entities\Experiment;
use Modules\Experiments\Entities\PhysicalDevice;

/**
* Command service
*/
class CommandService
{
	protected $experiment;
	protected $input;
	protected $user;
	protected $physicalDevice;

	public function __construct(Experiment $experiment, array $input = [])
	{
		$this->experiment = $experiment;
		$this->input = $input;
		$this->user = Auth::user()->user;
		$this->physicalDevice = $this->pickPhysicalDevice();
	}

	public function stop()
	{
		$server = new Server($this->physicalDevice->server->ip);
		
		$device = $this->input['device'];
		$software = $this->input['software'];
		$instance = $this->input['instance'];

		return $server->stopCommand($device, $software, $instance);
	}

	public function change()
	{
		$server = new Server($this->physicalDevice->server->ip);
		
		$device = $this->input['device'];
		$software = $this->input['software'];
		$instance = $this->input['instance'];
		$input = Arr::get($this->input['input'], 'change');

		return $server->changeCommand($device, $software, $instance, $input);
	}

	protected function pickPhysicalDevice()
	{
		$instanceName = Arr::get($this->input,"instance");
		
		return PhysicalDevice::ofDevice($this->input['device'])->ofName($instanceName)->firstOrFail();
	}
}