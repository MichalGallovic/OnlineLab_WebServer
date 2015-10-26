<?php namespace Modules\Reservation\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class ReservationController extends Controller {
	
	public function index()
	{
		return view('reservation::index');
	}
	
}