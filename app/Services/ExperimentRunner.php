<?php

namespace App\Services;

use App\User;
use Illuminate\Support\Arr;
use App\Services\ReportService;
use App\Services\SystemService;
use App\Classes\ApplicationServer\Server;
use Modules\Experiments\Entities\Experiment;
use App\Exceptions\Experiments\DeviceNotReady;
use Modules\Experiments\Entities\PhysicalDevice;
use Modules\Experiments\Entities\PhysicalExperiment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\Experiments\DeviceReservedForThisTime;

/**
* Experiment Runner
*/
class ExperimentRunner
{
	/**
	 * User that requested the experiment
	 * @var App\User
	 */
	protected $user;

	/**
	 * Experiment type that is requested
	 * @var Modules\Experiments\Entities\Experiment
	 */
	protected $experiment;

	/**
	 * Input for the experiment
	 * @var arrays
	 */
	protected $input;

	public function __construct(User $user, Experiment $experiment, $input)
	{
		$this->user = $user;
		$this->experiment = $experiment;
		$this->input = $input;
		$this->duration = $this->parseDuration();
	}

	public function queue()
	{
		$physicalDevice = $this->pickPhysicalDeviceForQueue();
		$physicalExperiment = $this->pickPhysicalExperiment($physicalDevice);
		$this->preCreateReport($physicalExperiment);

	    $server = new Server($physicalDevice->server->ip);
	    $server->queueExperiment($this->input);

	    if($server->success()) {
	        $physicalDevice->status = "experimenting";
	        $physicalDevice->save();
	    } else {
	    	$system = new SystemService();
	    	$system->syncWithServers();
	    	// server is not responding ???
	    	var_dump("server offline ajajaaj");
	    	throw new \Exception;
	    }
	}

	public function run()
	{
		$physicalDevice = $this->pickPhysicalDeviceForRealtime();
		$physicalExperiment = $this->pickPhysicalExperiment($physicalDevice);
		$this->preCreateReport($physicalExperiment);

	    $server = new Server($physicalDevice->server->ip);
	    $server->queueExperiment($this->input);

	    if($server->success()) {
	        $physicalDevice->status = "experimenting";
	        $physicalDevice->save();
	    } else {
	    	$system = new SystemService();
	    	$system->syncWithServers();
	    	// server is not responding ???
	    	var_dump("server offline ajajaaj");
	    	throw new \Exception;
	    }
	}

	/**
	 * Picks a requested device for experiment
	 * If the device is not ready, throws
	 * DeviceNotReady exception
	 * @return 
	 */
	protected function pickPhysicalDeviceForQueue()
	{
		// Check if nothing is reserved for this time
		// Nothing is reserved in duration + before_reservation time
		// Check if nothing is reserved ahaed of time
		// number of minutes to check ahead is defined
		// in Experiments module.json file

		$instanceName = Arr::get($this->input,"instance");
		
		$query = PhysicalDevice::ofDevice($this->input['device']);

		if($instanceName) {
			$query = $query->ofName($instanceName);
		}

		$possibleDevices = $query->get();

		$this->areThereFreeDevices($query);
		$this->arentDevicesReserved($query, $possibleDevices);
		$this->areDevicesReady($query);		


		return $query->first();
	}

	protected function pickPhysicalDeviceForRealtime()
	{
		// Check if nothing is reserved for this time
		// Nothing is reserved in duration + before_reservation time
		// Check if nothing is reserved ahaed of time
		// number of minutes to check ahead is defined
		// in Experiments module.json file

		$instanceName = Arr::get($this->input,"instance");
		
		$query = PhysicalDevice::ofDevice($this->input['device']);

		if($instanceName) {
			$query = $query->ofName($instanceName);
		}

		$possibleDevices = $query->get();

		$this->areThereFreeDevices($query);
		// $this->isntDeviceReserved($query, $possibleDevices);
		$this->areDevicesReady($query);		


		return $query->first();
	}

	protected function areThereFreeDevices($query)
	{
		// Ther eshould be at least one physicaldevice queryable at this
		// section of code
		// If it is 0, it means there was some kind of error, end we should fail
		if($query->count() == 0) {
			var_dump('Yep. Error ... possible server outage. :D');
			throw new \Exception;
		}
	}

	protected function arentDevicesReserved($query, $possibleDevices)
	{
		// ziskat dobu simulacie z inputu pre experiment
		// zo start commandu vyparsovat meaning "experiment_duration"
		if($query->notReserved($this->duration)->count() == 0) {
			throw new DeviceReservedForThisTime($possibleDevices, $this->duration);
		}
	}

	protected function areDevicesReady($query)
	{
		if($query->ready()->count() == 0) {
			throw new DeviceNotReady;
		}
	}

	protected function pickPhysicalExperiment(PhysicalDevice $physicalDevice)
	{
		return PhysicalExperiment::where('experiment_id', $this->experiment->id)->where('physical_device_id', $physicalDevice->id)->first();
	}

	protected function preCreateReport($physicalExperiment)
	{
		$report = new ReportService();
		$reportId = $report->create($this->user, $physicalExperiment, $this->input);
		$this->input = array_merge($this->input, [
	        "report_id" => $reportId
	    ]);
	}

	protected function parseDuration()
	{
		return intval(Arr::get($this->input, 'duration',0));
	}
	
}