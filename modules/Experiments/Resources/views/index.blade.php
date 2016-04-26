@extends('user.layouts.default')

@section('content')
	<div class="panel panel-primary">
		<div class="panel-heading">
			<div class="clearfix">
				<h3 class="panel-title pull-left">Servers</h3>
				<a href="{{ route('servers.sync') }}" class="btn btn-info btn-xs pull-right marginl20"><i class="glyphicon glyphicon-sort"></i> Sync servers</a>
				<a href="{{ route('servers.create') }}" class="btn btn-success btn-xs pull-right"><i class="glyphicon glyphicon-plus"></i> Add server</a>
			</div>
		</div>
		@include("experiments::servers.partials.index")
	</div>

	<div class="panel panel-primary">
		<div class="panel-heading">
			<div class="clearfix">
				<h3 class="panel-title pull-left">Admin experiments</h3>
			</div>
		</div>
		@include("experiments::experiments.partials.admin_index",['experiments' => $adminExperiments])
	</div>

	<div class="panel panel-primary">
		<div class="panel-heading">
			<div class="clearfix">
				<h3 class="panel-title pull-left">User experiments</h3>
			</div>
		</div>
		@include("experiments::experiments.partials.user_index",['experiments' => $userExperiments])
	</div>
@stop

@section('page_js')
	@parent
	<script type="text/javascript" src="{{ asset('js/animations.js') }}"></script>
@stop