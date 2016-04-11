<?php namespace Modules\Controller\Http\Controllers;

use Modules\Controller\Entities\Regulator;
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
		return view('controller::index', compact('myRegulators', 'publicRegulators', 'pendingRegulators'));
	}

	public function show($id) {
		$regulator=Regulator::find($id);
		return view('controller::show',compact('regulator'));
	}

	public function edit($id) {
		$regulator=Regulator::find($id);
		return view('controller::edit',compact('regulator'));
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
		return view('controller::create', compact('enviroment'));
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
			$regulator->system_id = Input::get('system_id');
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
			$regulator->system_id = 1;//$request->system_id;
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