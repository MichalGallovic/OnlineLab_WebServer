<?php namespace Modules\Experiments\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Services\SystemService;
use Illuminate\Support\Collection;
use Modules\Experiments\Entities\Device;
use Modules\Experiments\Entities\Server;
use Pingpong\Modules\Routing\Controller;
use Modules\Experiments\Entities\Software;
use Modules\Experiments\Entities\Experiment;
use Modules\Experiments\Entities\PhysicalDevice;
use Modules\Experiments\Entities\ServerExperiment;
use Modules\Experiments\Http\Requests\ServerRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ExperimentsController extends Controller {
	
	public function index()
	{
		$servers = Server::all();

		$adminExperiments = Experiment::has('physicalDevices')->get();
		$userExperiments = Experiment::has('physicalDevices')->whereHas('servers', function($q) {
			$q->available();
		})->get();

		return view('experiments::index', compact('servers', 'adminExperiments','userExperiments'));
	}

	public function getExperiments($softwareId){
		return Experiment::where('software_id', $softwareId)->get();
	}
}