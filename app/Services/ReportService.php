<?php

namespace App\Services;

use App\User;
use Modules\Report\Entities\Report;
use Modules\Experiments\Entities\ServerExperiment;
/**
* Report Service
*/
class ReportService
{
	protected $report;

	public function __construct(Report $report = null)
	{
		$this->report = $report;
	}

	public function create(User $user, ServerExperiment $instance, array $input)
	{
		$this->report = new Report;
		$this->report->experimentInstance()->associate($instance);
		$this->report->user()->associate($user);
		$this->report->input = $input;
		$this->report->save();

		return $this->report->id;
	}

	public function update(array $output, $simulationTime, $samplingRate)
	{
		if($this->report) {
			$this->report->output = $output;
			$this->report->simulation_time = $simulationTime;
			$this->report->sampling_rate = $samplingRate;
			$this->report->save();
		}

		if($output) {
			$this->report->filled = true;
			$this->report->save();
		}
	}
}