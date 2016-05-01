<?php namespace Modules\Controller\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Controller\Entities\Schema;
use Pingpong\Modules\Routing\Controller;
use Modules\Controller\Entities\Regulator;
use App\Http\Controllers\ApiBaseController;

class ApiController extends ApiBaseController {
	
	public function schema(Request $request, $id)
	{
		$schema = Schema::findOrFail($id);

		return response()->download($schema->getFilePath());
	}

	public function regulator(Request $request, $id)
	{
		$regulator = Regulator::findOrFail($id);
		
		return response()->download($regulator->getFilePath());
	}
}