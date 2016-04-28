<?php namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Report\Entities\Report;
use Illuminate\Support\Facades\Auth;
use Pingpong\Modules\Routing\Controller;
use Modules\Report\Http\Requests\UpdateReportRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReportController extends Controller {
	
	public function index()
	{
		$user = Auth::user()->user;

		$reports = Report::ofUser($user)->orderBy('updated_at','desc')->paginate();

		return view('report::index', compact('reports'));
	}

	public function show(Request $request, $id)
	{
		$report = Report::findOrFail($id);

		return view('report::show', compact('report'));
	}

	public function update(UpdateReportRequest $request, $id)
	{
		$report = Report::findOrFail($id);
		$report->notes = $request->input('notes');
		$report->save();

		return [
			"success" =>  "Report notes updated!"
		];
	}

	public function delete(Request $request, $id)
	{
		$report = Report::findOrFail($id);

		$report->delete();
		return redirect()->back();
	}

	
}