<?php

namespace App\Services;

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

	public function create(ServerExperiment $instance, array $input)
	{
		$this->report = new Report;
		$this->report->experimentInstance()->associate($instance);
		$this->report->input = $input;
		$this->report->save();

		return $this->report->id;
	}

	public function update(array $output)
	{
		if($this->report) {
			$this->report->output = $output;
			$this->report->save();
		}
	}
}