@extends('user.layouts.default')

@section('content')
	<div id="report-app">
		<olm-graph 
			v-if="series"
			:description="description"
			:series="series"
		></olm-graph>

		<div class="row">
			<div class="col-lg-6">
				<form v-on:submit.prevent="update">
					<div class="form-group">
						<label>Notes</label>
						<div id="editor">
							{!! $report->notes !!}
						</div>
					</div>
					<div class="form-group">
						<button type="submit" class="btn btn-success btn-xs">Update</button>
					</div>
				</form>
			</div>
			<div class="col-lg-6">
				<label>Input</label>
				<div class="form-group" v-for="(name, command) in report.input">
					<span class="label label-primary">@{{ name }}</span>
					<div class="row">
						<div class="col-lg-6" v-for='input in command' style="margin-top:10px">
							<strong>@{{ input.title }}</strong> <span class="label label-default">@{{ input.data }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<template id="graph-template">
		<div v-el:graph class="olm-graph" v-show="series.length > 1">
			
		</div>
		<div class="olm-graph-placeholder" v-show="series.length <= 1">
			@if(!$report->filled)
				<h4>Experiment did not run yet!</h4>
			@else
				<span class="label label-danger">Error</span>
				<h4 style="margin-top:10px">There was an error during the run! No data!</h4>
			@endif
		</div>
	</template>
@stop

@section('page_css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/reports/summernote.css') }}">
@stop

@section('page_js')
	@parent
	<script type="text/javascript">
		Laravel.Report = {
			device: "{{ $report->physicalDevice->device->name }}",
			software: "{{ $report->physicalExperiment->experiment->software->name }}",
			physical_device: "{{ $report->physicalDevice->name }}",
			measuring_rate: "{{ $report->sampling_rate }}",
			input: {!! json_encode(Illuminate\Support\Arr::get($report->input,'input')) !!},
			output: {!! json_encode($report->output) !!},
			input_arguments: {!! json_encode($report->physicalExperiment->commands) !!},
			output_arguments: {!! json_encode($report->physicalExperiment->output_arguments) !!}
		};
	</script>
	<script type="text/javascript" src="{{ asset('js/experiments/js/vue.min.js') }}"></script>
	<script src="{{ asset('js/underscore-min.js') }}"></script>
	<script src="{{ asset('js/reports/highcharts.js') }}"></script>
	<script src="{{ asset('js/reports/summernote.min.js') }}"></script>
	<script src="{{ asset('js/noty/jquery.noty.packaged.min.js') }}"></script>
	<script src="{{ asset('js/noty/relax.js') }}"></script>
	<script src="{{ asset('js/noty/topRight.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/reports/report.js') }}"></script>
@stop