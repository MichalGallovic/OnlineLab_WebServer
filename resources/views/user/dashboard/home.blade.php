@extends('user.layouts.default')

@include('user.dashboard.navigation')

@section('content')
	<h3>Dávkové experimenty</h3>
	<div class="row" id="queueApp">
		{{ csrf_field() }}
		<div class="col-lg-6">
			<form class="form" v-on:submit.prevent="runExperiment">
				<div class="form-group">
					<label>Experiment</label>
					<select v-model="selectedExperiment" class="form-control">
						<option v-for='experiment in experiments' v-bind:value="experiment">@{{ experiment.device }} - @{{ experiment.software }}</option>
					</select>
				</div>
				<div class="form-group" v-show="experiments && selectedExperiment">
					<div class="row" style="margin-top:10px">
						<div v-el:input class="form-group">
							<label 
							class="control-label col-xs-12"
							>Run on instance</label>
							<div class="col-xs-12">
								<span v-for="(index, instance) in selectedExperiment.instances" >
									<label class="radio-inline">
									  <input v-model="selected.instance" type="radio" name="@{{ instance.name }}[]" value="@{{ instance.name }}"> @{{ instance.name }}
									  <span class="label label-warning" v-show="!instance.production">testing</span>
									</label>
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group" v-for="commandName in selectedExperiment.experiment_commands">
					<span 
					v-show="selectedExperiment.commands[commandName]" 
					class="label label-primary" 
					style="font-size: 13px;">@{{ commandName }}</span>
					<olm-input
							v-for="input in selectedExperiment.commands[commandName]"
							:label="input.title"
							:name="input.name"
							:type="input.type"
							:values="input.values"
							:placeholder="input.placeholder"
							:command="commandName"
							>
					</olm-input>
				</div>
				<div class="form-group" v-show="selectedExperiment.experiment_commands.length > 0">
					<button class="btn btn-success pull-right" type="submit">Request experiment</button>
				</div>
			</form>
		</div>
	</div>

	<template id="input-template">
		<div class="row" style="margin-top:10px">
			<div v-el:input class="form-group">
				<label 
				v-bind:class="{
					'col-xs-6' : (type == 'text' || type == 'select'),
					'col-xs-12' : (type != 'text')
				}" 
				class="control-label"
				>@{{ label }}</label>
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
					<textarea v-model="input" class="form-control" rows="3" placeholder="@{{ placeholder }}"></textarea>
				</div>
				<div class="col-xs-6" v-if="type == 'select'">
					<select class="form-control" name="@{{ name }}" v-model="input">
					  <option v-for="value in values">@{{ value }}</option>
					</select>
				</div>
				<div class="col-xs-12" v-if="type == 'file'">
					<input type="file" name="@{{ name }}" v-model="input">
					<p class="help-block">@{{ placeholder }}</p>
				</div>
			</div>
		</div>
	</template>
@endsection

@section('page_js')
	@parent
	<script type="text/javascript" src="{{ asset('js/animations.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/experiments/js/vue.min.js') }}"></script>
	<script src="{{ asset('js/underscore-min.js') }}"></script>
	<script src="{{ asset('js/noty/jquery.noty.packaged.min.js') }}"></script>
	<script src="{{ asset('js/noty/relax.js') }}"></script>
	<script src="{{ asset('js/noty/topRight.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/experiments/js/queue.js') }}"></script>
@stop
