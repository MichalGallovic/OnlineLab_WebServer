<?php namespace Modules\Controller\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class ControllerController extends Controller {
	
	public function index()
	{
		return view('controller::index');
	}
	
}