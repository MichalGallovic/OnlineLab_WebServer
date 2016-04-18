@extends('user.layouts.default')

@section('content')
	<div class="panel panel-primary">
		<div class="panel-heading">
			<div class="clearfix">
				<h3 class="panel-title pull-left">Servers</h3>
				<a href="{{ route('servers.sync') }}" class="btn btn-default btn-xs pull-right marginl20"><i class="glyphicon glyphicon-sort"></i> Sync experiments</a>
				<a href="{{ route('servers.refreshStatus') }}" class="btn btn-info btn-xs pull-right marginl20"><i class="glyphicon glyphicon-refresh"></i> Refresh status</a>
				<a href="{{ route('servers.create') }}" class="btn btn-success btn-xs pull-right"><i class="glyphicon glyphicon-plus"></i> Add server</a>
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

	<h3>Dávkové experimenty</h3>
	<div class="row" id="queueApp">
		{{ csrf_field() }}
		<div class="col-lg-6">
			<form class="form" v-on:submit.prevent="runExperiment">
				<div class="form-group">
					<label>Experiment</label>
					<select v-model="selectedExperiment" class="form-control">
						<option v-for='experiment in experiments' v-bind:value="experiment">@{{ experiment.device }} - @{{ experiment.software }} - @{{ experiment.server }}</option>
					</select>
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
@stop

@section('page_js')
	@parent
	<script type="text/javascript" src="{{ asset('js/animations.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/experiments/js/vue.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/experiments/js/queue.js') }}"></script>
@stop