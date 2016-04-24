@extends('user.layouts.default')

@section('content')
	<div id="reservation-app">
		<olm-calendar>
		</olm-calendar>
	</div>

	<template id="olm-reservation-edit">
		<div v-el:modal class="modal fade" tabindex="-1" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Edit reservation</h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-lg-12">
								<strong>Time</strong>
								<span class="label label-success" style="font-size:12px">@{{ selection.start.format('lll') }}</span>
								<span class="label label-danger" style="font-size:12px">@{{ selection.end.format('lll') }}</span>	
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<label>Device</label>
									<select v-model="selection.device" class="form-control">
										<option v-for='device in devices' v-bind:value="device">@{{ device }}</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<label>Software</label>
									<select v-model="selection.software" class="form-control">
										<option v-for='software in softwares' v-bind:value="software">@{{ software }}</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row" v-show="selectedSoftware">
							<div class="col-lg-12">
								<div class="form-group">
									<label>Device instance</label>
									<span v-for="instance in instances">
										<label class="radio-inline">
										  <input v-model="selection.instance" type="radio" value="@{{ instance }}"> @{{ instance }}
										</label>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Delete reservation</button>
						<button type="button" class="btn btn-success" v-on:click="saveReservation">Save</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	</template>

	<template id="olm-reservation-show">
		<div v-el:modal class="modal fade" tabindex="-1" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Reservation details</h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-lg-12">
								<strong>Time</strong>
								<span class="label label-success" style="font-size:12px">@{{ start.format('lll') }}</span>
								<span class="label label-danger" style="font-size:12px">@{{ end.format('lll') }}</span>	
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<label>Device</label>
									<span><strong>@{{ device }}</strong></span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<label>Software</label>
									<span><strong>@{{ software }}</strong></span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<label>Device instance</label>
									<span><strong>@{{ instance }}</strong></span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<label>User</label>
									<span><strong>@{{ user }}</strong></span>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	</template>
	<template id="olm-calendar">
		<div v-el:calendar>
			
		</div>
		<div v-el:modal class="modal fade" tabindex="-1" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Reserve device</h4>
					</div>
					<div class="modal-body" v-if="creating || editing">
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
									<select v-model="selected.device" class="form-control">
										<option v-for='device in filteredDevices' v-bind:value="device">@{{ device.name }}</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								<span v-for="(index, instance) in selected.device.instances" >
									<label class="radio-inline">
									  <input type="radio" v-model="selected.instance" value="@{{ instance }}"> @{{ instance }}
									</label>
								</span>
							</div>
						</div>
					</div>
					<div class="modal-body" v-if="showing">
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
									<span>@{{ selectedEvent.device.name }}</span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								<label>Instance</label>
								<span>@{{ selectedEvent.device.physical_device }}</span>
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
		<!-- <olm-reservation-show
		v-if="showingReservation"
		:start="selectedEvent.start"
		:end="selectedEvent.end"
		:device="selectedEvent.device"
		:software="selectedEvent.software"
		:instance="selectedEvent.instance"
		:user="selectedEvent.user"
		>
		</olm-reservation-show>

		<olm-reservation-edit
		v-if="editingReservation"
		:selection="selectedEvent"
		:devices="filteredDevices"
		:softwares="softwaresForDevice"
		:instances="instancesForExperiment"
		>
		</olm-reservation-edit> -->
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