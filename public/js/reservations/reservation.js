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
				selectedEvent: {
					start : null,
					end : null
				},
				experiments : null,
				devices : null,
				selectedDevice : null,
				selectedSoftware : null,
				selectedExperiment: null,
				selectedInstance : null,
				softwaresForDevice : null,
				instancesForExperiment: null
			};
		},
		ready : function() {
			var me = this;
			this.$calendar = $(this.$els.calendar);
			this.$modal = $(this.$els.modal);
			this.initPlugin();
			this.$modal.modal('show');

			this.selectedEvent.start = moment();
			this.selectedEvent.end = moment().add(10,'minutes');

			this.getExperiments().done(function(response) {
				var devices = [];
				response.data.forEach(function(experiment) {
					devices.push(experiment.device);
				});

				me.devices = devices.unique();
				me.experiments = response.data;
				me.selectedDevice = me.devices[0];
			});
		},
		methods : {
			initPlugin: function() {
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
						me.$modal.modal('show');
					}
				});
			},
			getExperiments: function() {
				return $.getJSON("/api/experiments");
			}
		},
		watch : {
			selectedDevice : function(newVal, oldVal) {
				var me = this;
				var experiments = this.experiments.filter(function(experiment) {
					return experiment.device == me.selectedDevice;
				});

				this.softwaresForDevice = experiments.map(function(experiment) {
					return experiment.software;
				});

				this.selectedSoftware = this.softwaresForDevice[0];
			},
			selectedSoftware: function(newVal, oldVal) {
				var me = this;

				var experiments = this.experiments.filter(function(experiment) {
					return experiment.device == me.selectedDevice &&
					experiment.software == me.selectedSoftware;
				});

				if(experiments.length > 0) {
					this.selectedExperiment = experiments[0];
					this.instancesForExperiment = this.selectedExperiment.instances;
				}

			}
		}
	});
	var vm = new Vue({
		el: "#reservation-app",
	});
})($);