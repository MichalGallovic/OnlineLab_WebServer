@extends('user.layouts.default')

@section('content')

	<div class="panel panel-primary">
		<div class="panel-heading">
			<div class="clearfix">
				<h3 class="panel-title pull-left">{{trans("controller::default.CTRL_HEADING_MY")}}</h3>
				<a href="{{route('controller.create', 'matlab')}}" class="btn btn-success btn-xs pull-right" >{{ trans("controller::default.NEW_CONTROLLER_TITLE") }}</a>
			</div>
		</div>
		@include('controller::partials.panel', ['regulators' => $myRegulators])
	</div>

	<div class="panel panel-primary">
		<div class="panel-heading">
			<div class="clearfix">
				<h3 class="panel-title pull-left">{{trans("controller::default.CTRL_HEADING_PUBILC")}}</h3>
			</div>
		</div>
		@include('controller::partials.panel', ['regulators' => $publicRegulators])
	</div>

	@if(Auth::user()->user->isAdmin())
	<div class="panel panel-primary">
		<div class="panel-heading">
			<div class="clearfix">
				<h3 class="panel-title pull-left">{{trans("controller::default.CTRL_HEADING_PUBILC_PENDING")}}</h3>
			</div>
		</div>
		<div class="panel-body breaker">
			<table class="table table-hover">
				<thead>
				<tr>
					<th class="col-sm-1">{{ trans("controller::default.ID") }}</th>
					<th class="col-sm-2">{{ trans("controller::default.CTRL_NAME") }}</th>
					<th class="col-sm-1">{{ trans("controller::default.LABEL_SYSTEM") }}</th>
					<th class="col-sm-2">{{ trans("controller::default.CTRL_DATE") }}</th>
					<th class="col-sm-2">{{ trans("controller::default.CTRL_AUTHOR") }}</th>
					<th class="col-sm-1">{{ trans("controller::default.CTRL_ACCESSIBILITY") }}</th>
					<th class="col-sm-1"></th>
					<th class="col-sm-1"></th>
					<th class="col-sm-1"></th>
				</tr>
				</thead>
				<tbody>

				@foreach ($pendingRegulators as $regulator)
					<tr>
						<td>{{ $regulator->id }}</td>
						<td>{{ $regulator->title }}</td>
						<td>{{$regulator->system_id}}</td>

						<td>{{ $regulator->created_at }}</td>
						<td>{{$regulator->user->getFullName()}}</td>
						<td>{{ $regulator->type }}</td>
						<td></td>

						<td class="col-md-1"><a href="{{url('controller',$regulator->id)}}" class="btn btn-sm btn-block btn-primary"><span class="glyphicon glyphicon-search"></span> {{ trans("controller::default.PREVIEW_TITLE") }}</a></td>
						<td class="col-md-1">
							{!! Form::open(['method' => 'PATCH', 'route'=>['controller.approve', $regulator->id]]) !!}
							{!! Form::button('<span class="glyphicon glyphicon-ok"></span> '. trans("controller::default.CTRL_APPROVE"), ['class' => 'btn btn-sm btn-block btn-success', 'type'=>'submit']) !!}
							{!! Form::close() !!}
						</td>

					</tr>
				@endforeach

				</tbody>
				<tfoot>
				<tr>
					<th>{{ trans("controller::default.ID") }}</th>
					<th>{{ trans("controller::default.CTRL_NAME") }}</th>
					<th>{{ trans("controller::default.LABEL_SYSTEM") }}</th>
					<th>{{ trans("controller::default.CTRL_DATE") }}</th>
					<th>{{ trans("controller::default.CTRL_AUTHOR") }}</th>
					<th>{{ trans("controller::default.CTRL_ACCESSIBILITY") }}</th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
				</tfoot>
			</table>
		</div>
	</div>

	<div class="panel panel-primary">
		<div class="panel-heading">
			<div class="clearfix">
				<h3 class="panel-title pull-left">{{trans("controller::default.CTRL_HEADING_SCHEMA")}}</h3>
				<a href="#" class="btn btn-success btn-xs pull-right" data-toggle="modal" data-target="#upload-modal">{{trans("controller::default.CTRL_UPLOAD_SCHEMA")}}</a>
			</div>
		</div>
		<div class="panel-body breaker">
			<table class="table table-hover">
				<thead>
				<tr>
					<th class="col-sm-1">{{ trans("controller::default.CTRL_SCHEMA_ID") }}</th>
					<th class="col-sm-2">{{ trans("controller::default.CTRL_SCHEMA_TITLE") }}</th>
					<th class="col-sm-1">{{ trans("controller::default.CTRL_SCHEMA_TYPE") }}</th>
					<th class="col-sm-2">{{ trans("controller::default.CTRL_DATE") }}</th>
					<th class="col-sm-2"></th>
					<th class="col-sm-1"></th>
					<th class="col-sm-1"></th>
					<th class="col-sm-1"></th>
					<th class="col-sm-1"></th>
				</tr>
				</thead>
				<tbody>

				@foreach ($schemas as $schema)
					<tr>
						<td>{{ $schema->id }}</td>
						<td>{{ $schema->title }}</td>
						<td>{{ $schema->type }}</td>
						<td>{{ $schema->created_at }}</td>
						<td></td>
						<td></td>
						<td><a href="{{route('controller.schema.show',$schema->id)}}" class="btn btn-sm btn-block btn-info"><span class="glyphicon glyphicon-save-file"></span> {{ trans("controller::default.CTRL_DOWNLOAD_FILE") }}</a></td>
						<td><a href="{{route('controller.schema.edit',$schema->id)}}" class="btn btn-sm btn-block btn-warning"><span class="glyphicon glyphicon-edit"></span> {{ trans("controller::default.SETTINGS_TITLE") }}</a></td>
						<td class="col-md-1">
							{!! Form::open(['method' => 'DELETE', 'route'=>['controller.schema.destroy', $schema->id]]) !!}
							{!! Form::button('<span class="glyphicon glyphicon-remove"></span> '.trans("controller::default.TRASH_TITLE"), ['class' => 'btn btn-sm btn-block btn-danger', 'type'=>'submit']) !!}
							{!! Form::close() !!}
						</td>

					</tr>
				@endforeach

				</tbody>
				<tfoot>
				<tr>
					<th>{{ trans("controller::default.CTRL_SCHEMA_ID") }}</th>
					<th>{{ trans("controller::default.CTRL_SCHEMA_TITLE") }}</th>
					<th>{{ trans("controller::default.CTRL_SCHEMA_TYPE") }}</th>
					<th>{{ trans("controller::default.CTRL_DATE") }}</th>
					<th></th>
					<th></th>
				</tr>
				</tfoot>
			</table>
		</div>
	</div>
	@endif

	<div class="modal fade" id="upload-modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true"&times;></span>
						<span class="sr-only">Close</span>
					</button>
					<h4 class="modal-title">{{trans('controller::default.CTRL_SCHEMA_NEW')}}</h4>
				</div>
				<div class="modal-body">
					{!! Form::open(array('method' => 'post', 'route' => 'controller.schema.store', 'id' => 'file-upload-form', 'files' => true)) !!}
					<div class="form-group {!! ($errors->has('title')) ? 'has-error' : '' !!}">
						{!! Form::label("title", trans("controller::default.CTRL_SCHEMA_TITLE"), ['class' => 'control-label']) !!}
						{!! Form::text('title', '', ['class' => 'form-control']) !!}
						@if($errors->has('title'))
							<p>{!! $errors->first('title') !!}</p>
						@endif
					</div>
					<div class="form-group {!! ($errors->has('experiment_id')) ? 'has-error' : '' !!}">
						{!! Form::label("software", trans("controller::default.CTRL_SCHEMA_SOFTWARE"), ['class' => 'control-label']) !!}
						{!! Form::select("software", $softwares, null, ['class' => 'form-control', 'id' => 'software']) !!}
					</div>

					<div class="form-group">
						{!! Form::label("experiment_id", trans("controller::default.CTRL_SCHEMA_TYPE"), ['class' => 'control-label']) !!}
						{!! Form::select("experiment_id", $experiments, null, ['class' => 'form-control', 'id' => 'experiment']) !!}
					</div>

					<div class="form-group">
						{!! Form::label("type", trans("controller::default.CTRL_SCHEMA_TYPE"), ['class' => 'control-label']) !!}
						{!! Form::select("type", [trans("controller::default.CTRL_SCHEMA_TEXT") => trans("controller::default.CTRL_SCHEMA_TEXT_LEGEND"), trans("controller::default.CTRL_SCHEMA_FILE") => trans("controller::default.CTRL_SCHEMA_FILE_LEGEND"), trans("controller::default.CTRL_SCHEMA_NONE") => trans("controller::default.CTRL_SCHEMA_NONE_LEGEND")], null, ['class' => 'form-control']) !!}
					</div>

					<div class="form-group {!! ($errors->has('filename')) ? 'has-error' : '' !!}">
						{!! Form::label("filename", trans('controller::default.CTRL_SCHEMA'), ["class" => "control-label"]) !!}
						{!! Form::file("filename", ["class" => "form-control"]) !!}
						@if($errors->has('filename'))
							<p>{!! $errors->first('filename') !!}</p>
						@endif
					</div>
					<div class="form-group {!! ($errors->has('image')) ? 'has-error' : '' !!}">
						{!! Form::label("image", trans('controller::default.CTRL_SCHEMA_IMG'), ["class" => "control-label"]) !!}
						{!! Form::file("image", ["class" => "form-control"]) !!}
						@if($errors->has('image'))
							<p>{!! $errors->first('image') !!}</p>
						@endif
					</div>
					{!! Form::close() !!}
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{trans('controller::default.BACK_TO_CONTROLLERS')}}</button>
					{!! Form::submit(trans('controller::default.CTRL_SAVE'), array('form' => 'file-upload-form', 'class' => 'btn btn-primary')) !!}
				</div>
			</div>
		</div>
	</div>
@stop



@section('page_js')
	@parent
		<script type="text/javascript">
			@if(Session::has('modal'))
				$("{!! Session::get('modal') !!}").modal('show');
			@endif

            $('#software').on('change', function() {

				$.get('experiments/software/'+this.value, function(data, status){
					$('#experiment').empty();
					$.each(data , function(i, val) {
						$('#experiment').append($('<option>', {
							text: val.device.name,
							value: val.id
						}))
					});
				});
			});
		</script>
@stop