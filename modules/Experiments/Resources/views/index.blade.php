@extends('user.layouts.default')

@section('content')
	<div class="panel panel-primary">
		<div class="panel-heading">
			<div class="clearfix">
				<h3 class="panel-title pull-left">Servers</h3>
				<a href="{{ route('experiments.server.sync') }}" class="btn btn-default btn-xs pull-right marginl20">Sync servers</a>
				<a href="{{ route('experiments.server.create') }}" class="btn btn-success btn-xs pull-right">Add server</a>
			</div>
		</div>
		@include("experiments::servers.partials.index")
	</div>

	<div class="panel panel-primary">
		<div class="panel-heading">
			<div class="clearfix">
				<h3 class="panel-title pull-left">Experiments</h3>
			</div>
		</div>
		@include("experiments::experiments.partials.index")
	</div>
@stop