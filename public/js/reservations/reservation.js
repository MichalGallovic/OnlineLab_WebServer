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

!(function($) {
	Vue.config.devtools = true;
	Vue.component('olm-calendar', {
		template: '#olm-calendar',
		data : function() {
			return {
				$calendar : null,
				$modal : null,
				reservations : null,
				selectedEvent: {
					start : null,
					end : null,
					saving : false
				},
				experiments : {
					original: null,
					filtered: null
				},
				filteredDevices: null,
				devices : null,
				reservations : null,
				events : null,
				selectedDevice : null,
				selectedSoftware : null,
				selectedExperiment: null,
				selectedInstance : null,
				softwaresForDevice : null,
				instancesForExperiment: null,
			};
		},
		ready : function() {
			var me = this;
			this.$calendar = $(this.$els.calendar);
			this.$modal = $(this.$els.modal);
			
			// this.$modal.modal('show');

			this.selectedEvent.start = moment();
			this.selectedEvent.end = moment().add(10,'minutes');

			this.getExperiments().done(function(response) {
				var devices = [];

				response.data.forEach(function(experiment) {
					devices.push(experiment.device);
				});

				me.devices = devices.unique();
				me.experiments.original = response.data;
				me.experiments.filtered = JSON.parse(JSON.stringify(me.experiments.original)); // copy in VUE :/
			});

			this.getReservations().done(function(response) {
				var events = [];
				me.reservations = response.data;
				response.data.forEach(function(reservation) {
					var event = {
						id : reservation.id,
						title : reservation.device + " " + reservation.software + " " + reservation.instance,
						start : reservation.start,
						end : reservation.end
					};
					events.push(event);
				});

				me.events = events;
				me.initPlugin(me.events);
			});

			this.$modal.on('hide.bs.modal', function(e) {
				if(!me.selectedEvent.saving) {
					me.$calendar.fullCalendar('removeEvents',me.selectedEvent.id);
				}
			});
		},
		methods : {
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
						start : me.selectedEvent.start.format('YYYY-MM-DD hh:mm:ss'),
						end : me.selectedEvent.end.format('YYYY-MM-DD hh:mm:ss')
					}
				}).done(function(response) {
					me.$modal.modal('hide');
				});
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
						me.selectedEvent.start = start;
						me.selectedEvent.end = end;
						me.selectedEvent.saving = false;
						me.experiments.filtered = JSON.parse(JSON.stringify(me.experiments.original));

						me.experiments.filtered = me.experiments.filtered.map(function(experiment) {
							experiment.instances = experiment.instances.filter(function(instance) {
								return !me.isExperimentReserved(start,end,experiment,instance);
							});
							return experiment;
						});

						me.experiments.filtered = me.experiments.filtered.filter(function(experiment) {
							return experiment.instances.length > 0;
						});

						var devices = [];

						me.experiments.filtered.forEach(function(experiment) {
							devices.push(experiment.device);
						});

						me.filteredDevices = devices.unique();

						me.selectedDevice = me.filteredDevices[0];
						me.filterSoftwares();
						me.filterInstances();

						var maxId = 0;

						me.reservations.forEach(function(reservation) {
							if(reservation.id > maxId) {
								maxId = reservation.id;
							}
						});

						me.selectedEvent.id = ++maxId;
						me.selectedEvent.title = me.selectedDevice + " " + me.selectedSoftware + " " + me.selectedInstance;

						me.$calendar.fullCalendar('renderEvent',me.selectedEvent, true);
						me.$modal.modal('show');
					},
					events : events
				});
			},
			getExperiments: function() {
				return $.getJSON("/api/experiments");
			},
			getReservations: function() {
				return $.getJSON('/api/reservations');
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
			}
		}
	});
	var vm = new Vue({
		el: "#reservation-app",
	});
})($);