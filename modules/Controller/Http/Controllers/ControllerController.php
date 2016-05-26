<?php namespace Modules\Controller\Http\Controllers;

use Modules\Controller\Entities\Regulator;
use Modules\Controller\Entities\Schema;
use Modules\Experiments\Entities\Experiment;
use Modules\Experiments\Entities\Software;
use Pingpong\Modules\Routing\Controller;
use Auth;
use Input;
use Validator;
use File;
use Illuminate\Http\Request;

class ControllerController extends Controller {
	
	public function index() {
		$myRegulators = Auth::user()->user->regulators;
		$publicRegulators = Regulator::where('type','public')->get();
		$pendingRegulators = Regulator::where('type','public_pending')->get();
		$schemas = Schema::with('experiment.software')->get();
		$softwares = Software::where("hasRegulators", true)->lists('name', 'id');

		$experiments = Experiment::with('device')->whereHas('software', function($q) use ($softwares){
			$q->where('name', $softwares ? $softwares->first() : '');
		})->join('devices', 'devices.id', '=', 'experiments.device_id')->lists('name', 'experiments.id');

		return view('controller::index', compact('myRegulators', 'publicRegulators', 'pendingRegulators', 'schemas', 'experiments', 'softwares'));
	}

	public function show($id) {
		$regulator=Regulator::find($id);
		return view('controller::show',compact('regulator'));
	}

	public function edit($id) {
		$schemas = Schema::whereNotIn('type', Auth::user()->user->isAdmin() ? [''] : ['none'])->lists('title', 'id');
		$regulator=Regulator::find($id);
		$schema = $regulator->schema;
		return view('controller::edit',compact('regulator', 'schemas', 'schema'));
	}

	public function update($id, Request $request) {
		$validator = Validator::make(Input::all(), array(
			'title' => 'required',
			'body' => 'required',
			'system_id' => 'required',
			'type' => 'required'
		));

		if ($validator->fails()) {
			return redirect()->route('controller.edit', $id)
				->withErrors($validator)
				->withInput();
		} else {
			$userRegulator = Request::all();
			$regulator = Regulator::find($id);
			if ($regulator->update($userRegulator)) {
				File::put(storage_path().'/user_uploads/'.$regulator->user->id.'/regulators/'.$regulator->id.'/'.$regulator->id.'.txt', $regulator->body);
				return redirect()->route('controller.edit', $regulator->id)->with('success', trans('controller::default.CTRL_EDIT_SUCCESS'));
			} else {
				return redirect()->route('controller.edit', Input::get('enviroment'))
					->withInput()
					->with('fail', trans('controller::default.CTRL_EDIT_FAIL'));
			}
		}
	}

	public function create($enviroment=null){

		$softwares = Software::whereHas('experiments', function($query){
			$query->whereHas('schemas', function($query){
				if(!Auth::user()->user->isAdmin()){
					$query->where("type", "!=", "none");
				}
			});
		})->lists('name');

		if($softwares->count()>0){
			if(!$enviroment){
				$enviroment = $softwares->first();
			}
			$schemas = Schema::whereHas('experiment', function($query) use ($enviroment){
				$query->whereHas('software', function($q) use ($enviroment){
					$q->where('name', $enviroment);
				});
			})->whereNotIn('type', Auth::user()->user->isAdmin() ? [''] : ['none'])->lists('title', 'id');

			if(count($schemas)>0){
				$schema = Schema::whereHas('experiment', function($query) use ($enviroment){
					$query->whereHas('software', function($q) use ($enviroment){
						$q->where('name', $enviroment);
					});
				})->whereNotIn('type', Auth::user()->user->isAdmin() ? [''] : ['none'])->first();
				return view('controller::create', compact('enviroment', 'softwares', 'schemas', 'schema'));
			}else{
				return view('controller::error', compact('enviroment'));
			}
		}

		$enviroment = 'error';
		return view('controller::error', compact('enviroment'));

	}

	public function store(Request $request) {

		$validArray = [
			'title' => 'required',
			'schema_id' => 'required',
			'type' => 'required'
		];

		$schemaType = Schema::find($request->schema_id)->type;

		if($schemaType == 'text'){
			$validArray['body'] = 'required';
		} else if($schemaType == 'file'){
			$validArray['filename'] = 'required';
		}

		$validator = Validator::make($request->all(), $validArray);

		if ($validator->fails()) {
			return redirect()->route('controller.create', Input::get('enviroment'))
				->withInput()
				->withErrors($validator);
		} else {
			$regulator = new Regulator();
			$regulator->title = $request->title;
			$regulator->user_id = Auth::user()->user->id;
			$regulator->schema_id = $request->schema_id;
			$regulator->body = $request->body ? $request->body : null;
			if($request->openmodelica_final){
				$regulator->body = $request->openmodelica_final;
			}
			$regulator->filename = $request->filename ? $request->file('filename')->getClientOriginalName() : null;
			if(!Auth::user()->user->isAdmin() && $request->type == 'public'){
				$regulator->type = "public_pending";
			}else{
				$regulator->type = $request->type;
			}
			if($regulator->save()) {
				$path = storage_path().'/user_uploads/'.$regulator->user->id.'/regulators/'.$regulator->id.'/';
				if($schemaType == 'text'){
					$this->createDirectory($path);
					File::put($path . '/'.$regulator->id.'.txt', $regulator->body);
				}else if($schemaType == 'file'){
					$this->createDirectory($path);
					if ($request->file('filename')->isValid()) {
						$request->file('filename')->move($path, $request->file('filename')->getClientOriginalName()); // uploading file to given path
					}
				}

				return redirect()->route('controller.edit', $regulator->id)->with('success', trans('controller::default.CTRL_CREATE_SUCCESS'));
			} else {
				return redirect()->route('controller.create', Input::get('enviroment'))
					->withInput()
					->with('fail', trans('controller::default.CTRL_CREATE_FAIL'));
			}
		}
	}

	private function createDirectory($path){
		if(!File::Exists($path)){
			File::makeDirectory($path, 0775 , true);
		}
	}
/*
	public function upload(Request $request){
		$validator = Validator::make($request->all(), array(
			'title' => 'required',
			'filename' => 'required',
			//'system_id' => 'required',
			'type' => 'required'
		));

		if ($validator->fails()) {
			return redirect()->route('controller.index')
				->withInput()
				->withErrors($validator)
				->with('modal', '#upload-modal');
		} else {
			if ($request->file('filename')->isValid()) {
				$request->file('filename')->move(storage_path().'/user_uploads/'.Auth::user()->user->id.'/regulators/', $request->file('filename')->getClientOriginalName()); // uploading file to given path
			}
			$regulator = new Regulator();
			$regulator->title = $request->title;
			$regulator->user_id = Auth::user()->user->id;
			$regulator->schema_id = $request->schema;;
			$regulator->body = $request->body;
			$regulator->type = $request->type;
			$regulator->filename =  $request->file('filename')->getClientOriginalName();
			if($regulator->save()) {
				return redirect()->route('controller.index')->with('success', trans('controller::default.CTRL_CREATE_SUCCESS'));
			} else {
				return redirect()->route('controller.index')
					->withInput()
					->withErrors($validator)
					->with('fail', trans('controller::default.CTRL_CREATE_FAIL'));
			}
		}
	}
*/
	public function approve($id){
		$regulator = Regulator::find($id);
		$regulator->type = 'public';
		if($regulator->save()){
			return redirect()->route('controller.index')->with('success', trans('controller::default.CTRL_EDIT_SUCCESS'));
		}else{
			return redirect()->route('controller.index')->with('success', trans('controller::default.CTRL_EDIT_FAIL'));
		}
	}

	public function destroy($id){
		$regulator = Regulator::find($id);
		if($regulator->filename){
			File::Delete(storage_path().'/user_uploads/'.$regulator->user->id.'/regulators/'.$regulator->filename);
		}
		$regulator->delete();
		return redirect()->route('controller.index')->with('success', trans('controller::default.CTRL_DELETE_SUCCESS'));
	}
}