<?php namespace Modules\Profile\Http\Controllers;

use Module;
use Pingpong\Modules\Routing\Controller;

class ProfileController extends Controller {
	
	public function getSettings()
	{
		return view('profile::settings');
	}
	
}