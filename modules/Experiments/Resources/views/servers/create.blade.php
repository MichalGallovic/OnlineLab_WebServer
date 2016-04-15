@extends('user.layouts.default')

@section('content')
	<div class="row">
		<div class="col-lg-6">
			{!! Form::open(["rout" => route("experiments.server.store")]) !!}
			<div class="form-group">
				{!! Form::label("name", "Name", []) !!}
				{!! Form::text("name", "", [ "class" => "form-control"]) !!}
			</div>
			<div class="form-group">
				{!! Form::label("ip", "IP", []) !!}
				{!! Form::text("ip", "", [ "class" => "form-control"]) !!}
			</div>
			<div class="form-group">
				{!! Form::label("port", "Port", []) !!}
				{!! Form::text("port", "", [ "class" => "form-control"]) !!}
			</div>
			{!! Form::submit("Add Server", [ "class" => "btn btn-success" ]) !!}
			{!! Form::close() !!}
		</div>	
	</div>
@stop