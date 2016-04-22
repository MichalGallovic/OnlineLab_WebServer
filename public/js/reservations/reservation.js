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
}

var timeToLaravelString = function(momentInstance) {
		return momentInstance.format('YYYY-MM-DD hh:mm:ss')
};

!(function($) {
	Vue.config.devtools = true;
	Vue.component('olm-calendar', {
		template: '#olm-calendar',
		data : function() {
			return {
				$calendar : null,
				$modal : null,
				selectedEvent: {
					start : null,
					end : null,
					saving : false
				},
				reservations : null,
				experiments : {
					original: null,
					filtered: null
				},
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
			};
		},
		ready : function() {
			this.$calendar = $(this.$els.calendar);
			this.$modal = $(this.$els.modal);
			this.getExperimentsData();
			this.getReservationsData();
			this.setEventHandlers();
		},
		methods : {
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
					var events = [];
					me.reservations = response.data;
					response.data.forEach(function(reservation) {
						var event = {
							id : reservation.id,
							title : reservation.device + " " + reservation.software + " " + reservation.instance,
							start : reservation.start,
							end : reservation.end,
							device: reservation.device,
							software: reservation.software,
							instance: reservation.instance
						};
						events.push(event);
					});

					me.events = events;
					me.initPlugin(me.events);
				});
			},
			getExperiments: function() {
				return $.getJSON("/api/experiments");
			},
			getReservations: function() {
				return $.getJSON('/api/reservations');
			},
			setEventHandlers: function() {
				var me = this;
				this.$modal.on('hide.bs.modal', function(e) {
					if(!me.selectedEvent.saving) {
						me.$calendar.fullCalendar('removeEvents',me.selectedEvent.id);
						me.selectedEvent = null;
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
					me.$calendar.fullCalendar('removeEvents',me.selectedEvent.id);
					me.selectedEvent.id = response.id;
					me.$calendar.fullCalendar('renderEvent',{
						id : response.id,
						title: me.selectedEvent.title,
						start: me.selectedEvent.start,
						end: me.selectedEvent.end
					});
					me.$modal.modal('hide');
				});
			},
			experimentsCopy : function() {
				return JSON.parse(JSON.stringify(this.experiments.original))
			},
			isExperimentReserved : function(start, end, experiment, instance) {
				var timesNotCollide = function(start, end, reservationStart, reservationEnd) {
					return (start.isBefore(reservationStart) && end.isBefore(reservationStart)) ||
					(start.isAfter(reservationEnd) && end.isAfter(reservationEnd));
				}

				var me = this;
				return this.reservations.some(function(reservation) {
					return reservation.device == experiment.device &&
					reservation.software == experiment.software &&
					!timesNotCollide(start, end, reservation.start, reservation.end) &&
					reservation.instance == instance;
				});
			},
			filterDataForSelection : function(start, end) {
				var me = this;
				this.selectedEvent = {
					start : start,
					end : end,
					saving : false 
				}
				
				this.experiments.filtered = this.experimentsCopy();

				this.experiments.filtered = this.experiments.filtered.map(function(experiment) {
					experiment.instances = experiment.instances.filter(function(instance) {
						return !me.isExperimentReserved(start,end,experiment,instance);
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
				this.$calendar.fullCalendar({
					header: {
						left: 'prev,next today',
						center: 'title',
						right: 'month,agendaWeek,agendaDay'
					},
					slotDuration: '00:10:00',
					editable: true,
					eventLimit: true, // allow "more" link when too many events
					selectable: true,
					selectHelper: true,
					select: function(start, end) {
						me.filterDataForSelection(start, end);
						me.$calendar.fullCalendar('renderEvent',me.selectedEvent);
						me.refreshCalendar();
						me.$modal.modal('show');
					},
					eventClick: function(event, element) {
						console.log(event);
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

				this.selectedSoftware = this.softwaresForDevice[0];
			},
			filterInstances: function() {
				var me = this;

				this.experiments.filtered.forEach(function(experiment) {
					if(experiment.device == me.selectedDevice &&
					   experiment.software == me.selectedSoftware) {
						me.selectedExperiment = experiment;
						me.selectedInstance = me.selectedExperiment.instances[0];
					}
				});
			}
		},
		watch : {
			selectedDevice : function(newVal, oldVal) {
				this.filterSoftwares();
			},
			selectedSoftware: function(newVal, oldVal) {
				this.filterInstances();
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