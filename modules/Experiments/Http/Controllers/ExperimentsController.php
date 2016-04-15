<?php namespace Modules\Experiments\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Pingpong\Modules\Routing\Controller;
use App\Classes\ApplicationServer\Server;
use App\Classes\ApplicationServer\System;
use Modules\Experiments\Http\Requests\ServerRequest;


class ExperimentsController extends Controller {
	
	public function index()
	{
		return view('experiments::index');
	}
	
	public function refresh()
	{

	}

	public function createServer()
	{
		// $ips = ["http://192.168.100.100","http://192.168.100.110"];

		// $system = new System($ips);
		// dd($system->experiments());

		// $devices = $experiments->unique('device')->values()->lists("device");
		// $softwares = $experiments->unique('software')->values()->lists("software");


		return view('experiments::servers.create');
	}

	public function storeServer(ServerRequest $request)
	{
		dd($request->all());
	}
}