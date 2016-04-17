<?php namespace Modules\Experiments\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Services\SystemService;
use Illuminate\Support\Collection;
use Modules\Experiments\Entities\Device;
use Pingpong\Modules\Routing\Controller;
use App\Classes\ApplicationServer\Server;
use App\Classes\ApplicationServer\System;
use Modules\Experiments\Entities\Software;
use Modules\Experiments\Entities\Experiment;
use Modules\Experiments\Entities\ServerExperiment;
use Modules\Experiments\Http\Requests\ServerRequest;
use Modules\Experiments\Entities\Server as ServerModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ExperimentsController extends Controller {
	
	public function index()
	{
		$servers = ServerModel::all();
		$experiments = Experiment::where('available',true)->get();

		return view('experiments::index', compact('servers', 'experiments'));
	}

	public function sync()
	{
		$system = new SystemService();
		$system->syncWithServers();

		return redirect()->back();
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
		ServerModel::create($request->all());

		return redirect()->route('experiments.index');
	}
}