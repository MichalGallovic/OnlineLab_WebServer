<?php namespace Modules\Experiments\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SystemService;
use Modules\Experiments\Entities\Server;
use Pingpong\Modules\Routing\Controller;
use Modules\Experiments\Entities\PhysicalExperiment;
use Modules\Experiments\Http\Requests\ServerRequest;
use Modules\Experiments\Http\Requests\ServerUpdateRequest;

class ServersController extends Controller {
	
	
	public function sync()
	{
		$system = new SystemService();
		$system->syncWithServers();

		return redirect()->back();
	}

	
	public function refresh()
	{
		$system = new SystemService();
		$system->updateAvailability();

		return redirect()->back();
	}

	public function create()
	{
		return view('experiments::servers.create');
	}

	public function store(ServerRequest $request)
	{
		Server::create($request->all());

		return redirect()->route('experiments.index');
	}

	public function edit(Request $request, $id)
	{
		$server = Server::findOrFail($id);
		return view('experiments::servers.edit', compact('server'));
	}

	public function update(ServerUpdateRequest $request, $id)
	{
		$server = Server::findOrFail($id);
		$server->update($request->all());

		return redirect()->route("experiments.index");
	}

	public function destroy(Request $request, $id)
	{
		$server = Server::findOrFail($id);
		$physicalExperiments = PhysicalExperiment::where('server_id',$server->id)->delete();
		$server->physicalDevices()->delete();
		$server->delete();

		// $system = new SystemService();
		// $system->syncWithServers();

		return redirect()->back();
	}

	public function disable(Request $request, $id)
	{
		$server = Server::findOrFail($id);
		$server->disabled = true;
		$server->save();

		return redirect()->back();
	}

	public function enable(Request $request, $id)
	{
		$server = Server::findOrFail($id);
		$server->disabled = false;
		$server->save();

		$system = new SystemService();
		$system->syncWithServers();

		return redirect()->back();
	}
	
}