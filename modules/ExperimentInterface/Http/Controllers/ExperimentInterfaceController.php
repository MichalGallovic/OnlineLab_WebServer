<?php namespace Modules\Experimentinterface\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class ExperimentInterfaceController extends Controller {
	
	public function index()
	{
		return view('experimentinterface::index');
	}
	
}