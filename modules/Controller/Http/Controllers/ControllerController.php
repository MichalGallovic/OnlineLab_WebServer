<?php namespace Modules\Controller\Http\Controllers;

use Modules\Controller\Entities\Regulator;
use Modules\Controller\Entities\Schema;
use Modules\Experiments\Entities\Experiment;
use Modules\Experiments\Entities\Software;
use Pingpong\Modules\Routing\Controller;
use Auth;
use Input;
use Validator;
use Illuminate\Http\Request;

class ControllerController extends Controller {
	
	public function index() {
		$myRegulators = Auth::user()->user->regulators;
		$publicRegulators = Regulator::where('type','public')->get();
		$pendingRegulators = Regulator::where('type','public_pending')->get();
		$schemas = Schema::all();
		$softwares = Software::lists('name', 'id');

		$experiments = Experiment::with('device')->whereHas('software', function($q){
			$q->where('name', 'matlab');
		})->join('devices', 'devices.id', '=', 'experiments.device_id')->lists('name', 'experiments.id');

		return view('controller::index', compact('myRegulators', 'publicRegulators', 'pendingRegulators', 'schemas', 'experiments', 'softwares'));
	}

	public function show($id) {
		$regulator=Regulator::find($id);
		return view('controller::show',compact('regulator'));
	}

	public function edit($id) {
		$schemas = Schema::lists('title', 'id');
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
				return redirect()->route('controller.edit', $regulator->id)->with('success', trans('controller::default.CTRL_EDIT_SUCCESS'));
			} else {
				return redirect()->route('controller.edit', Input::get('enviroment'))
					->withInput()
					->with('fail', trans('controller::default.CTRL_EDIT_FAIL'));
			}
		}
	}

	public function create($enviroment){
		$schemas = Schema::lists('title', 'id');
		$schema = Schema::first();
		return view('controller::create', compact('enviroment', 'schemas', 'schema'));
	}

	public function store() {

		$validator = Validator::make(Input::all(), array(
			'title' => 'required',
			'body' => 'required',
			'system_id' => 'required',
			'type' => 'required'
		));

		if ($validator->fails()) {
			return redirect()->route('controller.create', Input::get('enviroment'))
				->withInput()
				->withErrors($validator);
		} else {
			$regulator = new Regulator();
			$regulator->title = Input::get('title');
			$regulator->user_id = Auth::user()->user->id;
			$regulator->schema_id = Input::get('schema_id');
			$regulator->body = Input::get('body');
			$regulator->type = Input::get('type');
			if($regulator->save()) {
				return redirect()->route('controller.edit', $regulator->id)->with('success', trans('controller::default.CTRL_CREATE_SUCCESS'));
			} else {
				return redirect()->route('controller.create', Input::get('enviroment'))
					->withInput()
					->with('fail', trans('controller::default.CTRL_CREATE_FAIL'));
			}
		}
	}

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
			\File::Delete(storage_path().'/user_uploads/'.$regulator->user->id.'/regulators/'.$regulator->filename);
		}
		$regulator->delete();
		return redirect()->route('controller.index')->with('success', trans('controller::default.CTRL_DELETE_SUCCESS'));
	}
}