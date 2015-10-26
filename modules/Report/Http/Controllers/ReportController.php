<?php namespace Modules\Report\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class ReportController extends Controller {
	
	public function index()
	{
		return view('report::index');
	}
	
}