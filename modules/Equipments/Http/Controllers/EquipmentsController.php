<?php namespace Modules\Equipments\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class EquipmentsController extends Controller {
	
	public function index()
	{
		return view('equipments::index');
	}
	
}