<?php namespace Modules\Localization\Http\Controllers;

use Pingpong\Modules\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class LocalizationController extends Controller {
	
	public function switchLang($lang) {
		if(array_key_exists($lang, config('localization.langs'))) {
			Session::set('applocale', $lang);
		}

		return redirect()->back();
	}

	public function getSettings()
	{
		return "CESC";
	}
	
}