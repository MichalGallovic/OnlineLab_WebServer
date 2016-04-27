@extends('user.layouts.default')

@section('heading')
	<span>Experiments > Server > add</span>
@stop

@section('content')
	<div class="row">
		<div class="col-lg-12">
			{!! Form::open(["route" => "servers.store", "class" => "form-horizontal"]) !!}
			<div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}" >
				{!! Form::label("name", "Name", [ "class" => "control-label col-sm-3"]) !!}
				<div class="col-sm-6">
					{!! Form::text("name", "", [ "class" => "form-control"]) !!}
					{!! $errors->first('name', '<p class="help-block">:message</p>') !!}
				</div>
			</div>
			<div class="form-group {{ $errors->has('ip') ? 'has-error' : ''}}" >
				{!! Form::label("ip", "IP", [ "class" => "control-label col-sm-3"]) !!}
				<div class="col-sm-6">
					{!! Form::text("ip", "", [ "class" => "form-control"]) !!}
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
			<div class="form-group {{ $errors->has('node_port') ? 'has-error' : ''}}" >
				{!! Form::label("port", "NodeJS port", [ "class" => "control-label col-sm-3"]) !!}
				<div class="col-sm-6">
					{!! Form::text("node_port", "3000", [ "class" => "form-control"]) !!}
					{!! $errors->first('node_port', '<p class="help-block">:message</p>') !!}
				</div>
			</div>
			<div class="form-group {{ $errors->has('color') ? 'has-error' : ''}}" >
				{!! Form::label("color", "Color", [ "class" => "control-label col-sm-3"]) !!}
				<div class="col-sm-6">
					<input type="color" name="color"></input>
					{!! $errors->first('colof', '<p class="help-block">:message</p>') !!}
				</div>
			</div>
			<div class="form-group">
				{!! Form::label("", "Deployed for", [ "class" => "control-label col-sm-3"]) !!}
				<div class="col-sm-6">
					<div class="btn-group" data-toggle="buttons">
					  <label class="btn btn-sm btn-warning active">
					    <input type="radio" name="production" id="option1" autocomplete="off" value="0" checked> Testing
					  </label>
					  <label class="btn btn-sm btn-warning">
					    <input type="radio" name="production" id="option2" autocomplete="off" value="1"> Production
					  </label>
					</div>
				</div>
			</div>
			<div class="col-lg-9">
				<div class="form-group">
					{!! Form::submit("Add Server", [ "class" => "btn btn-success pull-right" ]) !!}
				</div>
			</div>
			{!! Form::close() !!}
		</div>	
	</div>
@stop