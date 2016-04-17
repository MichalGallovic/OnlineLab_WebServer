@extends('user.layouts.default')

@section('heading')
	<span>Experiments > Server > edit</span>
@stop

@section('content')
	<div class="row">
		<div class="col-lg-12">
			{!! Form::model($server, [
					"method"	=>  "PATCH",
					"class"		=>	"form-horizontal",
					"route"		=>	["servers.update", $server->id]
				]) !!}
			<div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}" >
				{!! Form::label("name", "Name", [ "class" => "control-label col-sm-3"]) !!}
				<div class="col-sm-6">
					{!! Form::text("name", null, [ "class" => "form-control"]) !!}
					{!! $errors->first('name', '<p class="help-block">:message</p>') !!}
				</div>
			</div>
			<div class="form-group {{ $errors->has('ip') ? 'has-error' : ''}}" >
				{!! Form::label("ip", "IP", [ "class" => "control-label col-sm-3"]) !!}
				<div class="col-sm-6">
					{!! Form::text("ip", null, [ "class" => "form-control"]) !!}
					{!! $errors->first('ip', '<p class="help-block">:message</p>') !!}
				</div>
			</div>
			<div class="form-group {{ $errors->has('port') ? 'has-error' : ''}}" >
				{!! Form::label("port", "Port", [ "class" => "control-label col-sm-3"]) !!}
				<div class="col-sm-6">
					{!! Form::text("port", "80", [ "class" => "form-control"]) !!}
					{!! $errors->first('port', '<p class="help-block">:message</p>') !!}
				</div>
			</div>
			<div class="col-lg-9">
				<div class="form-group">
					{!! Form::submit("Update Server", [ "class" => "btn btn-success pull-right" ]) !!}
				</div>
			</div>
			{!! Form::close() !!}
		</div>	
	</div>
@stop