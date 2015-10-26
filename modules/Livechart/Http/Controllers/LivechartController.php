<?php namespace Modules\Livechart\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class LivechartController extends Controller {
	
	public function index()
	{
		return view('livechart::index');
	}
	
}