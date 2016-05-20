<?php namespace Modules\Controller\Http\Controllers;

use Illuminate\Database\QueryException;
use Modules\Controller\Entities\Schema;
use Pingpong\Modules\Routing\Controller;
use Illuminate\Http\Request;
use Validator;
use Image;
use File;
use Modules\Controller\Entities\Regulator;
use Modules\Experiments\Entities\Software;
use Modules\Experiments\Entities\Experiment;

class SchemaController extends Controller {


	public function show($id){
		$regulator = Regulator::find($id);
		return view('controller::show', compact('regulator'));
	}

	public function edit($id){
		$schema=Schema::with('experiment.software')->find($id);
		$file = $schema->getFileContent();
		$schemas = Schema::all();
		$softwares = Software::where("hasRegulators", true)->lists('name', 'id');
		$experiments = Experiment::with('device')->whereHas('software', function($q) use ($schema){
			$q->where('name', $schema->experiment->software->name);
		})->join('devices', 'devices.id', '=', 'experiments.device_id')->lists('name', 'experiments.id');

		return view('controller::schema',compact('schema', 'file', 'softwares', 'schemas', 'experiments'));
	}

	public function getImage($id)
	{
		$schema = Schema::find($id);
		if($schema->image){
			$filepath = storage_path() . '/schemas/'.$schema->id.'/image/'.$schema->image;
		}else{
			$filepath = public_path() . '/pictures/noschema.jpg';
		}
		$img = Image::make($filepath);
		return $img->response();
	}

	public function update(Request $request){
		$schema=Schema::find($request->id);

		$validator = Validator::make($request->all(), array(
			'title' => 'required',
			'image' => 'image'
		));

		if ($validator->fails()) {
			return redirect()->route('controller.schema.edit', $request->id)
				->withInput()
				->withErrors($validator);
		} else {

			$directory = storage_path().'/schemas/'.$schema->id.'/';
			$schema->title = $request->title;
			$schema->type = $request->type;
			$schema->title = $request->title;
			$schema->type = $request->type;
			$schema->note = $request->note;
			$schema->experiment_id = $request->experiment_id;
			if ($request->file('filename') && $request->file('filename')->isValid()) {
				File::cleanDirectory($directory.'file/');
				$request->file('filename')->move($directory.'file/',$request->file('filename')->getClientOriginalName());
				$schema->filename = $request->file('filename')->getClientOriginalName();
			}

			if ($request->file('image') && $request->file('image')->isValid()) {
				File::cleanDirectory($directory.'image/');
				$request->file('image')->move($directory.'image/', $request->file('image')->getClientOriginalName());
				$schema->image = $request->file('image')->getClientOriginalName();
			}

			if($schema->save()) {

				return redirect()->route('controller.schema.edit', $request->id)->with('success', trans('controller::default.CTRL_SCHEMA_UPDATE_SUCCESS'));
			} else {
				return redirect()->route('controller.schema.edit', $request->id)
					->withInput()
					->with('fail', trans('controller.schema.edit::default.CTRL_SCHEMA_UPDATE_FAIL'));
			}
		}
	}

	public function store(Request $request) {

		$validator = Validator::make($request->all(), array(
			'title' => 'unique:schemas,title|required',
			'filename' => 'required',
			'image' => 'image'
		));

		if ($validator->fails()) {
			return redirect()->route('controller.index')
				->withInput()
				->withErrors($validator)
				->with('modal', '#upload-modal');
		} else {
			if ($request->file('filename')->isValid()) {
				$schema = new Schema();
				$schema->title = $request->title;
				$schema->type = $request->type;
				$schema->experiment_id = $request->experiment_id;
				$schema->note = $request->note;
				$schema->filename = $request->file('filename')->getClientOriginalName();
				if($request->image){
					$schema->image = $request->file('image')->getClientOriginalName();
				}
				if($schema->save()) {
					$request->file('filename')->move(storage_path().'/schemas/'.$schema->id.'/file/',$request->file('filename')->getClientOriginalName()); // uploading file to given path
					if ($request->file('image') && $request->file('image')->isValid()) {
						$request->file('image')->move(storage_path().'/schemas/'.$schema->id.'/image/', $request->file('image')->getClientOriginalName()); // uploading file to given path
					}
					return redirect()->route('controller.index')->with('success', trans('controller::default.CTRL_SCHEMA_CREATE_SUCCESS'));
				} else {
					return redirect()->route('controller.index')
						->withInput()
						->with('fail', trans('controller::default.CTRL_SCHEMA_CREATE_FAIL'));
				}
			}else{
				return redirect()->route('controller.index')
					->withInput()
					->with('fail', trans('controller::default.CTRL_SCHEMA_CREATE_FAIL'));
			}
		}
	}

	public function getData(Request $request){
		$schema = Schema::find($request->id);
		return ['fileContent' => $schema->getFileContent(), 'type' => $schema->type];
	}

	public function destroy($id){
		$schema = Schema::find($id);
		$id = $schema->id;
		try{
			$schema->delete();
		}catch(QueryException $e){
			return redirect()->route('controller.index')->with('fail', trans('controller::default.CTRL_SCHEMA_DELETE_FAIL'));
		}


		\File::Delete(storage_path().'/schemas/'.$id.'/');
		return redirect()->route('controller.index')->with('success', trans('controller::default.CTRL_SCHEMA_DELETE_SUCCESS'));
	}
}