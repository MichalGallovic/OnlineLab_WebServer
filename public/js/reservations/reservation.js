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
	// Vue.component('olm-reservation-edit', {
	// 	template: '#olm-reservation-edit',
	// 	props: ['devices','softwares','instances','user','selection'],
	// 	data: function() {
	// 		return {
	// 			$modal: null
	// 		};
	// 	},
	// 	ready: function() {
	// 		var me = this;
	// 		this.$modal = $(this.$els.modal);
	// 		this.$modal.modal('show');
	// 		this.$modal.on('hidden.bs.modal',function() {
	// 			me.$dispatch('edit-reservation','hidden');
	// 		});
	// 	}
	// });
	// Vue.component('olm-reservation-show', {
	// 	template: "#olm-reservation-show",
	// 	props: ['start','end','device','software','instance', 'user'],
	// 	data: function() {
	// 		return {
	// 			$modal: null
	// 		};
	// 	},
	// 	ready: function() {
	// 		var me = this;
	// 		this.$modal = $(this.$els.modal);
	// 		this.$modal.modal('show');
	// 		this.$modal.on('hidden.bs.modal',function() {
	// 			me.$dispatch('show-reservation','hidden');
	// 		});
	// 	}
	// });
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
				selected: {
					device: null,
					instance: null
				},
				reservations : null,
				devices : null,
				events : null,
				filteredDevices: null,
				showing: false,
				editing: false,
				creating: false
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
			flashSuccess: function(text) {
				noty ({
					text : text,
					theme: "relax",
					layout: "topRight",
					timeout : 5000,
					type: 'success'
				});
			},
			flashInfo: function(text) {
				noty ({
					text : text,
					theme: "relax",
					layout: "topRight",
					timeout : 5000,
					type: 'information'
				});
			},
			flashAlert: function(text) {
				noty ({
					text : text,
					theme: "relax",
					layout: "topRight",
					timeout : 5000,
					type: 'alert'
				});
			},
			flashWarning: function(text) {
				noty ({
					text : text,
					theme: "relax",
					layout: "topRight",
					timeout : 5000,
					type: 'warning'
				});
			},
			setEventHandlers: function() {
				var me = this;
				this.$modal.on('hide.bs.modal', function(e) {
					if(me.selectedEvent && !me.selectedEvent.saving && !me.showing && !me.editing) {
						me.$calendar.fullCalendar('removeEvents',me.selectedEvent.id);
					}
					me.selectedEvent = null;
					me.selected = {
						device: null,
						instance: null
					};
					me.showing = false;
					me.editing = false;
					me.creating = false;
				});


			},
			deleteReservation: function() {
				this.deleteReservationData(this.selectedEvent.id);
			},
			deleteReservationData: function(id) {
				var me = this;
				$.ajax({
					type: "DELETE",
					url: "/api/reservations/" + id
				}).done(function(response) {
					me.flashSuccess(response.success.message);
					me.refreshReservationsData();
					me.$modal.modal('hide');
				}).fail(function(response) {
					response = JSON.parse(response.responseText);
					me.flashWarning(response.error.message);
					me.$modal.modal('hide');
				});
			},
			updateReservation: function() {
				var device = {
					name : this.selected.device.name,
					physical_device: this.selected.instance
				}
				this.updateReservationData({
					start: this.selectedEvent.start,
					end: this.selectedEvent.end,
					device: device,
					id: this.selectedEvent.id
				});
			},
			updateReservationData: function(event, revert) {
				var me = this;
				$.ajax({
					type: "PUT",
					url: "/api/reservations/" + event.id,
					data: {
						device : event.device.name,
						physical_device: event.device.physical_device,
						start : timeToLaravelString(event.start),
						end : timeToLaravelString(event.end),
					}
				}).done(function(response) {
					me.flashSuccess(response.success.message);
					me.refreshReservationsData();
					me.$modal.modal('hide');
				}).fail(function(response) {
					response = JSON.parse(response.responseText);
					console.log(response);
					me.flashWarning(response.error.message);
					revert();
					me.$modal.modal('hide');
				});
			},
			saveReservation: function() {
				var me = this;
				this.selectedEvent.saving = true;
				var device = me.selected.device.name;
				var physicalDevice = me.selected.instance;
				var start = me.selectedEvent.start;
				var end = me.selectedEvent.end;
				$.ajax({
					type : "POST",
					url : '/api/reservations',
					data : {
						device : me.selected.device.name,
						physical_device: me.selected.instance,
						start : timeToLaravelString(me.selectedEvent.start),
						end : timeToLaravelString(me.selectedEvent.end),
					}
				}).done(function(response) {
					me.refreshReservationsData();
					me.$modal.modal('hide');
					var text = "Device " + device + ' ' + physicalDevice + " reserved";
					text += "<br>";
					text += "<strong>" + start.format('lll') + "</strong>" + " - <strong>" + end.format('lll') + "</strong>";
					me.flashSuccess(response.success.message);
				});
			},
			devicesCopy : function() {
				return JSON.parse(JSON.stringify(this.devices));
			},
			refreshCalendar: function() {
				if(this.selectedEvent) {
					this.$calendar.fullCalendar('removeEvents',this.selectedEvent.id);
					this.$calendar.fullCalendar('renderEvent',this.selectedEvent);
				}
				this.$calendar.fullCalendar('rerenderEvents');
			},
			isDeviceReserved : function(start, end, device, reservations) {
				var timesNotCollide = function(start, end, reservationStart, reservationEnd) {
					return (start.isBefore(reservationStart) && end.isSameOrBefore(reservationStart)) ||
					(start.isSameOrAfter(reservationEnd) && end.isAfter(reservationEnd));
				}

				var me = this;
				return reservations.some(function(reservation) {
					return reservation.device.name == device.name &&
					reservation.device.physical_device == device.physical_device &&
					!timesNotCollide(start, end, reservation.start, reservation.end);
				});
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
					select: function(start, end) {
						me.creating = true;
						me.selectedEvent = {
							id: (_.max(me.reservations,function(reservation){return reservation.id})+1),
							start : start,
							end : end,
							saving : false 
						};
						me.filteredDevices = me.devicesCopy();
						me.filteredDevices = me.filteredDevices.map(function(device) {
							device.instances = device.instances.filter(function(instance) {
								return !me.isDeviceReserved(start, end, {
									name: device.name,
									physical_device: instance
								}, me.reservations);
							});
							return device;
						});

						me.filteredDevices = me.filteredDevices.filter(function(device) {
							return device.instances.length > 0;
						});	

						if(me.filteredDevices.length > 0) {
							me.selected.device = me.filteredDevices[0];
							me.selected.instance = me.filteredDevices[0].instances[0];
							me.selectedEvent.title = me.selected.device.name + " " + me.selected.instance;
							me.$calendar.fullCalendar('renderEvent',me.selectedEvent);
							me.$modal.modal('show');
						} else {
							me.flashWarning("All devices reserved for this time.");
							me.$calendar.fullCalendar('unselect');
						}
					},
					eventClick: function(event, element) {
						me.selectedEvent = event;
						if(event.editable) {
							me.editing = true;
							me.filteredDevices = me.devicesCopy();
							var reservations = JSON.parse(JSON.stringify(me.reservations));
							var reservations = me.reservations.filter(function(reservation) {
								return reservation.id != event.id;
							});

							me.filteredDevices = me.filteredDevices.map(function(device) {
								device.instances = device.instances.filter(function(instance) {
									return !me.isDeviceReserved(event.start, event.end, {
										name: device.name,
										physical_device: instance
									}, reservations);
								});
								return device;
							});

							me.filteredDevices = me.filteredDevices.filter(function(device) {
								return device.instances.length > 0;
							});

							console.log(event.device.name, event.device.physical_device);

							me.selected.device = _.findWhere(me.filteredDevices, {
								name: event.device.name
							});

							me.selected.instance = event.device.physical_device;
						} else {
							me.showing = true;
						}
						me.$modal.modal('show');
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
						me.editing = true;
						me.updateReservationData(event, revertFunc);
					},
					eventResize: function(event, delta, revertFunc) {
						me.editing = true;
						me.updateReservationData(event, revertFunc);
					},
					events : events
				});
			}
		},
		watch: {
			selected: {
				handler: function(val, oldVal) {
					if(this.selected.device && !this.showing && !this.editing) {
						this.selectedEvent.title = this.selected.device.name + " " + this.selected.instance;
						this.refreshCalendar();
					}
				},
				deep:true
			}
		}
	
	});
	var vm = new Vue({
		el: "#reservation-app",
	});
})($);