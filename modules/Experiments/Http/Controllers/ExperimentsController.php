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
use Modules\Experiments\Entities\ServerExperiment;
use Modules\Experiments\Http\Requests\ServerRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ExperimentsController extends Controller {
	
	public function index()
	{
		$servers = Server::all();
		$experiments = Experiment::available()->get();
		$experimentInstances = ServerExperiment::available()->get();
		$adminExperimentInstances = ServerExperiment::all();

		return view('experiments::index', compact('servers', 'experiments','experimentInstances','adminExperimentInstances'));
	}
}