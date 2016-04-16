<?php namespace Modules\Experiments\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Modules\Experiments\Entities\Device;
use Pingpong\Modules\Routing\Controller;
use App\Classes\ApplicationServer\Server;
use App\Classes\ApplicationServer\System;
use Modules\Experiments\Entities\Software;
use Modules\Experiments\Entities\Experiment;
use Modules\Experiments\Http\Requests\ServerRequest;
use Modules\Experiments\Entities\Server as ServerModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Experiments\Entities\ServerExperiment;


class ExperimentsController extends Controller {
	
	public function index()
	{
		$servers = ServerModel::all();
		$experiments = Experiment::where('available',true)->get();

		return view('experiments::index', compact('servers', 'experiments'));
	}

	public function sync()
	{
		$servers = ServerModel::all();

		$ips = $servers->lists('ip');
		$system = new System($ips->all());

		$appServerExperiments = $system->uniqueExperiments();

		$webServerExperiments = Experiment::all();

		$newExperiments = new Collection();
		$webServerToActivate = new Collection();

		foreach ($appServerExperiments as $appServerExperiment) {
			$isNew = true;

			foreach ($webServerExperiments as $webServerExperiment) {
				if($webServerExperiment->device->name == $appServerExperiment["device"] &&
					$webServerExperiment->software->name == $appServerExperiment["software"]) {
					$webServerToActivate->push($webServerExperiment);
					$isNew = false;
					break;
				}
			}
			if($isNew) {
				$newExperiments->push($appServerExperiment);
			}
		}

		$webServerToDeactivate = $webServerExperiments->diff($webServerToActivate);

		foreach ($webServerToDeactivate as $experiment) {
			$experiment->available = false;
			$experiment->save();
		}

		foreach ($webServerToActivate as $experiment) {
			$experiment->available = true;
			$experiment->save();
		}

		foreach ($newExperiments as $experiment) {
			$device = Device::firstOrCreate([
					"name"	=>	$experiment["device"]
				]);
			$software = Software::firstOrCreate([
					"name"	=>	$experiment["software"]
				]);
			$experiment = new Experiment;
			$experiment->device()->associate($device);
			$experiment->software()->associate($software);
			$experiment->available = true;
			$experiment->save();
		}



		$experiments = Experiment::all();
		$exprimentInstances = $system->experiments()->groupBy('ip');

		$availableExperimentInstances = new Collection();

		foreach ($exprimentInstances as $ip => $serverExperiments) {
			foreach ($serverExperiments as $experiment) {
				$serverIp = str_replace("/", "", $ip);
				$server = $servers->where('ip',$serverIp)->first();
				
				$webServerExperiment = Experiment::whereHas("device", function($q) use ($experiment) {
					$q->where('name',$experiment["device"]);
				})->whereHas("software", function($q) use ($experiment) {
					$q->where('name',$experiment["software"]);
				})->first();
				
				$server_experiment = ServerExperiment::where("experiment_id",$webServerExperiment->id)->firstOrCreate([
						"server_id"	=>	$server->id,
						"experiment_id"	=>	$webServerExperiment->id
					]);

				$server_experiment->available = true;
				$server_experiment->save();

				$availableExperimentInstances->push($server_experiment);
			}
		}

		$experimentInstancesToDisable = ServerExperiment::all()->diff($availableExperimentInstances);
		foreach ($experimentInstancesToDisable as $experimentInstance) {
			$experimentInstance->available = false;
			$experimentInstance->save();
		}

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