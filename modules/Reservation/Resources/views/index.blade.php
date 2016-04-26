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
						<h4 class="modal-title" v-if="creating">Create reservation</h4>
						<h4 class="modal-title" v-if="editing">Edit reservation</h4>
						<h4 class="modal-title" v-if="showing">Reservation info</h4>
					</div>
					<div class="modal-body" v-if="creating || editing">
						<div class="row">
							<div class="col-lg-12">
								<h4>Time</h4>
								<p style="margin-top: 10px">
									<span class="label label-success label-xs" style="font-size:12px">@{{ selectedEvent.start.format('lll') }}</span>
									<span class="label label-danger" style="font-size:12px">@{{ selectedEvent.end.format('lll') }}</span>
								</p>	
							</div>
						</div>
						<div class="row" style="margin-top: 20px">
							<div class="col-lg-12">
								<div class="form-group">
									<h4>Device</h4>
									<select v-model="selected.device" class="form-control">
										<option v-for='device in filteredDevices' v-bind:value="device">@{{ device.name }}</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								<h4>Instance</h4>
								<span v-for="(index, instance) in selected.device.instances" >
									<label class="radio-inline">
									  <input type="radio" v-model="selected.instance" value="@{{ instance }}"> <span class="label label-primary">@{{ instance }}</span>
									</label>
								</span>
							</div>
						</div>
					</div>
					<div class="modal-body" v-if="showing">
						<div class="row">
							<div class="col-lg-12">
								<h4>Time</h4>
								<p style="margin-top: 10px">
									<span class="label label-success label-xs" style="font-size:12px">@{{ selectedEvent.start.format('lll') }}</span>
									<span class="label label-danger" style="font-size:12px">@{{ selectedEvent.end.format('lll') }}</span>
								</p>	
							</div>
						</div>
						<div class="row" style="margin-top:20px">
							<div class="col-lg-12">
								<div class="form-group">
									<h4>Device</h4>
									<span class="label label-default">@{{ selectedEvent.device.name }}</span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								<h4>Instance</h4>
								<span class="label label-primary">@{{ selectedEvent.device.physical_device }}</span>
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button v-if="editing" type="button" class="btn btn-danger" v-on:click="deleteReservation">Delete</button>
						<button v-if="editing" type="button" class="btn btn-success" v-on:click="updateReservation">Update</button>
						<button v-if="creating" type="button" class="btn btn-success" v-on:click="saveReservation">Reserve</button>
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
	<script src="{{ asset('js/underscore-min.js') }}"></script>
	<script src="{{ asset('js/noty/jquery.noty.packaged.min.js') }}"></script>
	<script src="{{ asset('js/noty/relax.js') }}"></script>
	<script src="{{ asset('js/noty/topRight.js') }}"></script>
	<script src="{{ asset('js/reservations/reservation.js') }}"></script>
@stop