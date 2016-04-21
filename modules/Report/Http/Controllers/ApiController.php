<?php namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportService;
use Modules\Report\Entities\Report;
use Pingpong\Modules\Routing\Controller;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ApiController extends ApiBaseController {
	
	public function update(Request $request, $id)
	{
		try {
			$report = Report::findOrFail($id);
			$reportService = new ReportService($report);
			$reportService->update(
				$request->input('report'), 
				$request->input("simulation_time"),
				$request->input("sampling_rate")
				);

		} catch(ModelNotFoundException $e) {
			return $this->errorNotFound("Report not found!");
		}

		return $this->respondWithSuccess("Report updated.");
	}

	
}