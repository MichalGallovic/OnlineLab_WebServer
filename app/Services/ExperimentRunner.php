<?php

namespace App\Services;

use App\User;
use Illuminate\Support\Arr;
use App\Services\ReportService;
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

	public function run()
	{
		$physicalDevice = $this->pickPhysicalDevice();
		$physicalExperiment = $this->pickPhysicalExperiment($physicalDevice);
		$this->preCreateReport($physicalExperiment);

	    $server = new Server($physicalDevice->server->ip);
	    $server->queueExperiment($this->input);

	    if($server->success()) {
	        $physicalDevice->status = "experimenting";
	        $physicalDevice->save();
	    } else {
	    	// server is not responding ???
	    }
	}

	/**
	 * Picks a requested device for experiment
	 * If the device is not ready, throws
	 * DeviceNotReady exception
	 * @return 
	 */
	protected function pickPhysicalDevice()
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

		if($query->ready()->count() == 0) {
			throw new DeviceNotReady;
		}

		// ziskat dobu simulacie z inputu pre experiment
		// zo start commandu vyparsovat meaning "experiment_duration"
		if($query->notReserved($this->duration)->count() == 0) {
			throw new DeviceReservedForThisTime($possibleDevices, $this->duration);
		}

		return $query->first();
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