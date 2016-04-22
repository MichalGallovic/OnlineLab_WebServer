@extends('user.layouts.default')

@section('content')
	<div id="reservation-app">
		<olm-calendar>
		</olm-calendar>
	</div>
	<template id="olm-calendar">
		<div v-el:calendar>
			
		</div>
		<div v-el:modal class="modal fade" tabindex="-1" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Reserve the experiment</h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-lg-12">
								<strong>Time</strong>
								<span class="label label-success" style="font-size:12px">@{{ selectedEvent.start.format('lll') }}</span>
								<span class="label label-danger" style="font-size:12px">@{{ selectedEvent.end.format('lll') }}</span>	
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<label>Device</label>
									<select v-model="selectedDevice" class="form-control">
										<option v-for='device in devices' v-bind:value="device">@{{ device }}</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row" v-show="selectedDevice && softwaresForDevice">
							<div class="col-lg-12">
								<div class="form-group">
									<label>Software</label>
									<select v-model="selectedSoftware" class="form-control">
										<option v-for='software in softwaresForDevice' v-bind:value="software">@{{ software }}</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row" v-show="selectedSoftware">
							<div class="col-lg-12">
								<div class="form-group">
									<label>Device instance</label>
									<span v-for="instance in selectedExperiment.instances">
										<label class="radio-inline">
										  <input v-model="selectedInstance" type="radio" value="@{{ instance }}"> @{{ instance }}
										</label>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-success" v-on:click="saveReservation">Reserve</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	</template>
@stop

@section("page_css")
	@parent
	<link rel="stylesheet" type="text/css" href="{{ asset('css/reservations/fullcalendar.min.css') }}">
@stop

@section("page_js")
	@parent
	<script type="text/javascript" src="{{ asset('js/experiments/js/vue.min.js') }}"></script>
	<script src="{{ asset('js/reservations/moment.min.js') }}"></script>
	<script src="{{ asset('js/reservations/fullcalendar.js') }}"></script>
	<script src="{{ asset('js/reservations/reservation.js') }}"></script>
@stop