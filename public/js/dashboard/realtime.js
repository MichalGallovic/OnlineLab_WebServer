!(function($) {
	Vue.config.devtools = true;
	Vue.component('olm-graph', {
		template: "#graph-template",
		props: {
			series: {
				default: function() {
					return [{data:[]}];
				}
			},
			description: {
				type: String,
				default: "Empty graph"
			}
		},
		ready: function() {
			this.initGraph(this.series);
		},
		methods: {
			initGraph: function(series) {
				var me = this;
				
				this.getjQueryGraph().highcharts({
					chart: {
						height:350
					},
					title: {
					    text: me.description
					},
					xAxis: {
						title: {
							text: "Simulation time"
						},
						labels: {
							formatter: function() {
								if(this.value <= 1000) {
									return this.value;
								}

								return this.value / 1000.00;
							}
						}
					},
					yAxis: {
					    title: {
					        text: 'Measurement value'
					    }
					},
					legend: {
						align: 'right',
			            verticalAlign: 'top',
			            layout: 'vertical',
			            x: 0,
			            y: 0,
			            itemMarginTop: 5
					},
					series: series
				});
			},
			getjQueryGraph: function() {
				return $(this.$els.graph);
			}
		},
		watch : {
			series: function(newSeries, oldSeries) {
				var chart = this.getjQueryGraph().highcharts();

				if(newSeries && oldSeries && (newSeries.length == oldSeries.length)) {
					for(var i = 0; i < newSeries.length; i++) {
						chart.series[i].setData(newSeries[i].data)
					}
				} else {
					chart.destroy();
					this.initGraph(newSeries);
				}

			},
		},
		events : {
			toggleLayout: function() {
				var me = this;
				setTimeout(function() {
					me.getjQueryGraph().highcharts().reflow();
				}, 100);
			}
		}
	});

	var vm = new Vue({
		el: "#realtime-app",
		data: {
			servers: null,
			user_id: null,
			selectedExperiment : null,
			experiments : null,
            physicalExperiments: null,
            selected : {
                instance : null,
                software: null,
                device: null,
                samplingRate: null
            },
            series: null,
            samplingRateField: null
		},
		ready: function() {
			this.init();
		},
		methods: {
			init: function() {
				var me = this;
				this.user_id = Laravel.Reservations[0].user_id;
				this.parseServers();
				this.getExperiments().done(function(response) {
	                me.physicalExperiments = _.chain(response.data).map(function(experiment) {
	                	experiment.commands = experiment.commands.data;
	                    _.object(_.map(experiment.commands,function(command) {
	                    	return command.map(function(input) {
	                    		if(input.type == "file" && input.meaning == 'parent_schema') {
	                    			input.type = "select";
	                    			input.values = ["Tak","To","urcite"];
	                    		}
	                    		if(input.type == "file" && input.meaning == 'child_schema') {
	                    			input.type = "select";
	                    			input.values = ["Tak","To","urcite","detska","schema"];
	                    		}
	                    		return input;
	                    	});
	                    }));
	                    experiment.experiment_commands = experiment.experiment_commands.data;
	                    experiment.output_arguments = experiment.output_arguments.data;
	                    return experiment;
	                }).value();

	                var experiments = JSON.parse(JSON.stringify(me.physicalExperiments));
	                me.experiments = _.chain(experiments).groupBy('experiment_id').map(function(experiments) {
	                    var experiment = _.first(experiments);
	                    experiment.instances = _.map(experiments, function(experiment) {
	                        return {
	                            name: experiment.physical_device,
	                            production: experiment.production
	                        }
	                    });
	                    return experiment;
	                }).value();

	    			me.selectedExperiment = me.experiments[0]
				}).then(this.initWebSockets);
			},
			parseServers: function() {
				this.servers = Laravel.Reservations.map(function(reservation) {
					return reservation.physical_device.server;
				});
			},
			initWebSockets: function() {
				var me = this;
				this.servers.forEach(function(server) {
					var socket = io(server.ip + ":" + server.node_port);
					socket.on('experiment-data:' + me.user_id, function(message) {

						me.series = me.formatGraphInput(
							message.data,
							me.selected.samplingRate,
							me.selectedExperiment.output_arguments
						);
					});
				});
			},
			getExperiments: function() {
				var ids = _.chain(Laravel.Reservations).pluck('physical_device').pluck('id').value();
				return $.getJSON('api/experiments?include=commands,experiment_commands,output_arguments&physical_device=' + ids.join(','));
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
			runExperiment: function() {
				var me = this;
				var promises = [];

				$.each(this.$children, function(index, component) {
					if($.isFunction(component.getInputValues)) {
						promises.push(component.getInputValues());
					}
				});

				$.when.apply($, promises).then(function() {
					var data = me.makeRequestData(arguments);
                    me.selected.samplingRate = parseInt(data.input.start[me.samplingRateField.name]);
                    console.log(data);
					// me.postRunExperiment(data)
					// .done(function(response) {
					// 	me.flashSuccess(response.success.message);
					// });
				});
			},
			makeRequestData: function(inputs) {
				var request = {
					device: this.selectedExperiment.device,
					software: this.selectedExperiment.software,
					input: {}
				};

				$.each(this.selectedExperiment.experiment_commands, function(index, command) {
					request.input[command] = {};
				});
				$.each(inputs, function(index, input) {
                    if(input.command) {
                        request.input[input.command][input.name] = input.value;
                    } 
				});

                request.instance = this.selected.instance;

				return request;
			},
			postRunExperiment: function(data) {
				var me = this;
				return $.ajax({
					"type" : "POST",
					"url" : "/api/experiments/" + me.selectedExperiment.experiment_id +"/run",
					"data" : data
				});
			},
			formatGraphInput: function(data, rate, output_arguments) {
				rate = parseInt(rate);
				var me = this;
				var series = [];
				var indexCounter = 0;
				data = data || [];
				$.each(data, function(index, measurement) {
					var measurementWithTime = [];
					$.each(measurement, function(indexCounter, value) {
						measurementWithTime.push([indexCounter*rate, parseFloat(value)]);
					});
					series.push({
						type: "line",
						name: output_arguments[indexCounter].title,
						data: measurementWithTime,
						visible: false
					});
					indexCounter++;
				});

				return series;
			},
		},
		watch: {
		    selectedExperiment: function(newSelection, oldVal) {
		        this.selected.instance = this.selectedExperiment.instances[0].name;
		        this.selected.software = this.selectedExperiment.software;
		        this.selected.device = this.selectedExperiment.device;
		        this.samplingRateField = _.findWhere(this.selectedExperiment.commands.start,{meaning: "sampling_rate"});
		    },
		    selected : {
		        handler: function(val, oldVal) {
		            var me = this;
		            if(Laravel.user.role == 'admin') {
		                var selectedExperiment = _.find(this.physicalExperiments, function(experiment) {
		                    return experiment.physical_device == me.selected.instance &&
		                    experiment.software == me.selected.software &&
		                    experiment.device == me.selected.device;
		                });

		                this.selectedExperiment.commands = selectedExperiment.commands;
		                this.selectedExperiment.experiment_commands = selectedExperiment.experiment_commands;
		            }
		        },
		        deep: true
		    }
		},
	});
})($);