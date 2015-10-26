<?php namespace Modules\Experiment\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class ExperimentController extends Controller {
	
	public function index()
	{
		return view('experiment::index');
	}
	
}