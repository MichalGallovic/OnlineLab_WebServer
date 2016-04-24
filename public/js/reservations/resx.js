Array.prototype.contains = function(v) {
    for(var i = 0; i < this.length; i++) {
        if(this[i] === v) return true;
    }
    return false;
};

Array.prototype.unique = function() {
    var arr = [];
    for(var i = 0; i < this.length; i++) {
        if(!arr.contains(this[i])) {
            arr.push(this[i]);
        }
    }
    return arr; 
};



var timeToLaravelString = function(momentInstance) {
		return momentInstance.format('YYYY-MM-DD HH:mm:ss')
};


function ColorLuminance(hex, lum) {

	// validate hex string
	hex = String(hex).replace(/[^0-9a-f]/gi, '');
	if (hex.length < 6) {
		hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
	}
	lum = lum || 0;

	// convert to decimal and change luminosity
	var rgb = "#", c, i;
	for (i = 0; i < 3; i++) {
		c = parseInt(hex.substr(i*2,2), 16);
		c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
		rgb += ("00"+c).substr(c.length);
	}

	return rgb;
}

!(function($) {
	Vue.config.devtools = true;
	Vue.component('olm-reservation-edit', {
		template: '#olm-reservation-edit',
		props: ['devices','softwares','instances','user','selection'],
		data: function() {
			return {
				$modal: null
			};
		},
		ready: function() {
			var me = this;
			this.$modal = $(this.$els.modal);
			this.$modal.modal('show');
			this.$modal.on('hidden.bs.modal',function() {
				me.$dispatch('edit-reservation','hidden');
			});
		}
	});
	Vue.component('olm-reservation-show', {
		template: "#olm-reservation-show",
		props: ['start','end','device','software','instance', 'user'],
		data: function() {
			return {
				$modal: null
			};
		},
		ready: function() {
			var me = this;
			this.$modal = $(this.$els.modal);
			this.$modal.modal('show');
			this.$modal.on('hidden.bs.modal',function() {
				me.$dispatch('show-reservation','hidden');
			});
		}
	});
	Vue.component('olm-calendar', {
		template: '#olm-calendar',
		data : function() {
			return {
				$calendar : null,
				$modal : null,
				$editModal: null,
				selectedEvent: {
					start : null,
					end : null,
					saving : false
				},
				selected: {
					device: null,
					instance: null
				},
				reservations : null,
				experiments : {
					original: null,
					filtered: null
				},
				physicalDevices: null,
				devices : null,
				reservations : null,
				events : null,
				filteredDevices: null,
				selectedDevice : null,
				selectedSoftware : null,
				selectedExperiment: null,
				selectedInstance : null,
				softwaresForDevice : null,
				instancesForExperiment: null,
				showingReservation: false,
				editingReservation: false,
				overlappingEvents: []
			};
		},
		ready : function() {
			this.$calendar = $(this.$els.calendar);
			this.$modal = $(this.$els.modal);
			// this.$editModal = $(this.$els.modalEdit);
			
			// this.getExperimentsData();
			this.getReservationsData();
			this.getDevicesData();
			this.setEventHandlers();
		},
		events: {
			'show-reservation': function(msg) {
				if(msg == 'hidden') this.showingReservation = false;
			},
			'edit-reservation': function(msg) {
				if(msg == 'hidden') this.editingReservation = false;	
			}
		},
		methods : {
			getDevicesData: function() {
				var me = this;
				this.getDevices().done(function(response) {
					me.devices = _.chain(response.data).groupBy('name').map(function(devices) {
						var device = _.first(devices);
						device.instances = _.pluck(devices,'physical_device');
						return device;
					}).value();
				});
			},
			getExperimentsData: function() {
				var me = this;
				this.getExperiments().done(function(response) {
					var devices = [];

					response.data.forEach(function(experiment) {
						devices.push(experiment.device);
					});

					me.devices = devices.unique();
					me.experiments.original = response.data;
					me.experiments.filtered = JSON.parse(JSON.stringify(me.experiments.original)); // copy in VUE :/
				});
			},
			getReservationsData: function() {
				var me = this;
				this.getReservations().done(function(response) {
					me.reservations = response.data;
					me.initPlugin(response.data);
				});
			},
			refreshReservationsData: function() {
				var me = this;
				this.getReservations().done(function(response) {
					me.reservations = response.data;
					me.$calendar.fullCalendar('removeEvents');
					me.$calendar.fullCalendar('addEventSource', response.data);
				});
			},
			getExperiments: function() {
				return $.getJSON("/api/experiments");
			},
			getReservations: function() {
				return $.getJSON('/api/reservations');
			},
			getDevices: function() {
				return $.getJSON('/api/devices');
			},
			setEventHandlers: function() {
				var me = this;
				this.$modal.on('hide.bs.modal', function(e) {
					if(!me.selectedEvent.saving) {
						me.$calendar.fullCalendar('removeEvents',me.selectedEvent.id);
						me.selectedEvent = null;
						me.selected = {
							device: null,
							instance: null
						}
					} else {
						me.refreshCalendar();
					}
				});


			},
			saveReservation: function() {
				var me = this;
				this.selectedEvent.saving = true;
				$.ajax({
					type : "POST",
					url : '/api/reservations',
					data : {
						device : me.selectedExperiment.device,
						software : me.selectedExperiment.software,
						instance : me.selectedInstance,
						start : timeToLaravelString(me.selectedEvent.start),
						end : timeToLaravelString(me.selectedEvent.end),
					}
				}).done(function(response) {
					me.refreshReservationsData();
					me.$modal.modal('hide');
				});
			},
			experimentsCopy : function() {
				return JSON.parse(JSON.stringify(this.experiments.original))
			},
			isExperimentReserved : function(start, end, experiment, instance, reservations) {
				var timesNotCollide = function(start, end, reservationStart, reservationEnd) {
					return (start.isBefore(reservationStart) && end.isBefore(reservationStart)) ||
					(start.isAfter(reservationEnd) && end.isAfter(reservationEnd));
				}

				var me = this;
				return reservations.some(function(reservation) {
					return reservation.device == experiment.device &&
					reservation.software == experiment.software &&
					!timesNotCollide(start, end, reservation.start, reservation.end) &&
					reservation.instance == instance;
				});
			},
			filterDataForSelection : function(start, end, reservations) {
				var me = this;
				this.selectedEvent = {
					start : start,
					end : end,
					saving : false 
				}
				
				this.experiments.filtered = this.experimentsCopy();

				this.experiments.filtered = this.experiments.filtered.map(function(experiment) {
					experiment.instances = experiment.instances.filter(function(instance) {
						return !me.isExperimentReserved(start,end,experiment,instance,reservations);
					});
					return experiment;
				});

				this.experiments.filtered = this.experiments.filtered.filter(function(experiment) {
					return experiment.instances.length > 0;
				});

				var devices = [];

				this.experiments.filtered.forEach(function(experiment) {
					devices.push(experiment.device);
				});

				this.filteredDevices = devices.unique();

				this.selectedDevice = this.filteredDevices[0];
				this.filterSoftwares();
				this.filterInstances();
				var maxId = 0;

				this.reservations.forEach(function(reservation) {
					if(reservation.id > maxId) {
						maxId = reservation.id;
					}
				});

				this.selectedEvent.id = ++maxId;
				this.selectedEvent.title = this.selectedDevice + " " + this.selectedSoftware + " " + this.selectedInstance;
			},
			refreshCalendar: function() {
				if(this.selectedEvent) {
					this.$calendar.fullCalendar('removeEvents',this.selectedEvent.id);
					this.$calendar.fullCalendar('renderEvent',this.selectedEvent);
				}
				this.$calendar.fullCalendar('rerenderEvents');
			},
			initPlugin: function(events) {
				var me = this;
				var height = $(window).height() - $("#dashboard_header").height() - 40;
				this.$calendar.fullCalendar({
					header: {
						left: 'prev,next today',
						center: 'title',
						right: 'month,agendaWeek,agendaDay'
					},
					allDaySlot: false,
					slotDuration: '00:10:00',
					editable: true,
					eventLimit: true, // allow "more" link when too many events
					selectable: true,
					backgroundColor : "#5cb85c",
					height: height,
					// selectHelper: true,
					select: function(start, end) {
						me.selectedEvent = {
							start : start,
							end : end,
							saving : false 
						}
						// me.filterDataForSelection(start, end, me.reservations);
						// me.$calendar.fullCalendar('renderEvent',me.selectedEvent);
						me.$modal.modal('show');
					},
					selectOverlap: function(event) {
						me.overlappingEvents.push(event);
						me.overlappingEvents = me.overlappingEvents.unique();
						return event;
					},
					unselect: function() {
						me.overlappingEvents = [];
					},
					eventClick: function(event, element) {
						me.selectedEvent = event;
						console.log(event);
						if(event.editable) {
							var reservations = JSON.parse(JSON.stringify(me.reservations));
							reservations = reservations.filter(function(reservation) {
								return reservation.id != event.id;
							});
							me.filterDataForSelection(event.start, event.end, reservations);
							me.editingReservation = true;
						} else {
							me.showingReservation = true;
						}

						return false;
					},
					eventMouseover: function(event, jsEvent, view) {
						var darkerColor = ColorLuminance(event.backgroundColor, -0.1);
						$(this).css({
							'background': darkerColor
						});
					},
					eventMouseout: function(event) {
						$(this).css({
							'background' : event.backgroundColor
						});
					},
					eventDrop: function(event, delta, revertFunc) {
						$.ajax({
							type: "PUT",
							url: "/api/reservations/" + event.id,
							data: {
								device: event.device,
								software: event.software,
								instance: event.instance,
								start: timeToLaravelString(event.start),
								end: timeToLaravelString(event.end)
							}
						}).done(function(response) {

						}).fail(function(response) {
							revertFunc();
						});
					},
					eventResize: function(event, delta, revertFunc) {
						$.ajax({
							type: "PUT",
							url: "/api/reservations/" + event.id,
							data: {
								device: event.device,
								software: event.software,
								instance: event.instance,
								start: timeToLaravelString(event.start),
								end: timeToLaravelString(event.end)
							}
						}).done(function(response) {

						}).fail(function(response) {
							revertFunc();
						});
					},
					events : events
				});
			},
			filterSoftwares: function() {
				var me = this;
				var experiments = this.experiments.filtered.filter(function(experiment) {
					return experiment.device == me.selectedDevice;
				});

				this.softwaresForDevice = experiments.map(function(experiment) {
					return experiment.software;
				});
			},
			filterInstances: function() {
				var me = this;

				this.experiments.filtered.forEach(function(experiment) {
					if(experiment.device == me.selectedDevice &&
					   experiment.software == me.selectedSoftware) {
						me.selectedExperiment = experiment;
					}
				});				
			}
		},
		watch : {
			selectedDevice : function(newVal, oldVal) {
				this.filterSoftwares();
				this.selectedSoftware = this.softwaresForDevice[0];
			},
			selectedSoftware: function(newVal, oldVal) {
				this.filterInstances();
				this.selectedInstance = this.selectedExperiment.instances[0];
			},
			selectedInstance : function(newVal, oldVal) {
				this.selectedEvent.title = this.selectedDevice + " " + this.selectedSoftware + " " + newVal;
				this.refreshCalendar();
			}
		}
	});
	var vm = new Vue({
		el: "#reservation-app",
	});
})($);