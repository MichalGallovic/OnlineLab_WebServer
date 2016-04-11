<?php namespace Modules\Test\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class TestController extends Controller {
	
	public function index()
	{
		return view('test::index');
	}
	
}