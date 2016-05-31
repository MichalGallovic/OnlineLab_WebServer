@extends('user.layouts.default')

@include('user.dashboard.navigation')

@section('content')
	@if(!$reservations->isEmpty())
	<div class="row">
		<div class="col-lg-12">
			<h4>{!! trans('dashboard.realtime') !!}</h4>
		</div>
		@include('user.partials.realtimeExperiments')
	</div>
	@else
	<h4>{!! trans('dashboard.norealtime') !!}</h4>
	<a style="font-size:15px" href="{{ url('reservation/calendar') }}">{!! trans('dashboard.reservations') !!}</a>
	@endif
	<div class="row" style="margin-top:20px;">
		<div class="col-lg-6">
			@include('user.partials.queueExperiments')
		</div>
	</div>


	<template id="input-template">
		<div class="row" style="margin-top:10px" v-show="visible">
			<div v-el:input class="form-group">
				<div
				v-bind:class="{
					'col-xs-6' : (type == 'text' || type == 'select'),
					'col-xs-12' : (type != 'text')
				}" v-if="meaning != 'child_schema'">
					<label class="control-label">@{{ label }}</label>
				</div>
				<div
				v-bind:class="{
					'col-xs-6' : (type == 'text' || type == 'select'),
					'col-xs-12' : (type != 'text')
				}" v-if="meaning == 'child_schema' && values.length > 1">
					<label class="control-label">@{{ label }}</label>
					
				</div>

				<div class="col-xs-6" v-if="type == 'text'">
					<input v-model="input" class="form-control" type="text" name="@{{ name }}" placeholder="@{{ placeholder }}" value="@{{ placeholder }}">
				</div>
				<div class="col-xs-12" v-if="type == 'radio'">
					<span v-for="(index, value) in values" >
						<label class="radio-inline">
						  <input v-model="input" type="radio" name="@{{ name }}[]" value="@{{ value }}"> @{{ value }}
						</label>
					</span>
				</div>
				<div class="col-xs-12" v-if="type == 'checkbox'">
					<span v-for="(index, value) in values" >
						<label class="checkbox-inline" for="@{{ name}}@{{index}}">
						  <input id="@{{ name}}@{{index}}" v-model="input" type="checkbox" name="@{{ name }}[]" value="@{{ value }}"> @{{ value }}
						</label>
					</span>
				</div>
				<div class="col-xs-12" v-if="type == 'textarea'">
					<textarea v-model="input" class="form-control" rows="3" placeholder="@{{ placeholder }}">@{{ placeholder }}</textarea>
				</div>
				<div class="col-xs-6" v-if="type == 'select'">
					<select class="form-control" name="@{{ name }}" v-model="input" v-if="!meaning">
					  <option v-for="value in values" v-if="typeof value == 'string'">@{{ value }}</option>
					</select>
					<select class="form-control" name="@{{ name }}" v-if="meaning == 'parent_schema'" v-model="input">
					  <option v-for="value in values" v-if="typeof value == 'object'" v-bind:value="value.data">@{{ value.name }}</option>
					</select>
					<select class="form-control" name="@{{ name }}" v-if="meaning == 'child_schema' && visible" v-model="input">
					  <option v-for="value in values" v-if="typeof value == 'object'" v-bind:value="value.data">@{{ value.name }}</option>
					</select>
				</div>

				<div class="col-xs-12" v-if="type == 'file'">
					<input type="file" name="@{{ name }}" v-model="input">
					<p class="help-block">@{{ placeholder }}</p>
				</div>
			</div>
		</div>
	</template>
	<template id="webcam-template">
		<div id="narrow">
           <img id="mjpeg_dest" />
       </div>
	</template>
@endsection

@section('page_css')
	<link rel="stylesheet" type="text/css" href="{{ asset('css/dashboard/style.css') }}">
@stop

@section('page_js')
	@parent
	<script type="text/javascript" src="{{ asset('js/animations.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/experiments/js/vue.min.js') }}"></script>
	<script src="{{ asset('js/underscore-min.js') }}"></script>
	<script src="{{ asset('js/noty/jquery.noty.packaged.min.js') }}"></script>
	<script src="{{ asset('js/noty/relax.js') }}"></script>
	<script src="{{ asset('js/noty/topRight.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/experiments/js/queue.js') }}"></script>
	<script src="{{ asset('js/dashboard/highcharts.js') }}"></script>
	<script src="{{ asset('js/dashboard/socket.io-1.4.5.js') }}"></script>
	<script>
		Laravel.Reservations = {!! $reservations->toJson() !!}
	</script>
	<script src="{{ asset('js/dashboard/realtime.js') }}"></script>
@stop
