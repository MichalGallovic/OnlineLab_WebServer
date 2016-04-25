@extends('user.layouts.default')

@section('content')
	<div class="panel panel-primary">
		<div class="panel-heading">
			<div class="clearfix">
				<h3 class="panel-title pull-left">Reports</h3>
			</div>
		</div>
		@include("report::reports.partials.index")
	</div>
@stop