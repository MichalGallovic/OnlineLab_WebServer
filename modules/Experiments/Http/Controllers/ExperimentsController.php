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
		$experiments = Experiment::available()->get();

		return view('experiments::index', compact('servers', 'experiments'));
	}
}