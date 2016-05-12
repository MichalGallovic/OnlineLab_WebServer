!(function($) {
	Vue.config.devtools = true;
	    Vue.component('olm-input',{
	    	template: "#input-template",
	    	props: {
	    		label:null,
	    		type : {
	    			default : function() {
	    				return "text";
	    			}
	    		},
	    		placeholder: {
	    			default : function() {
	    				return "This is placeholder";
	    			}
	    		},
	    		values: [],
	    		name : null,
	    		command: null,
	            default: null,
	            meaning: null,
	            visible: true
	    	},
	    	data: function() {
	    		return {
	    			input : null
	    		}
	    	},
	    	ready: function() {

	    	},
	        events: {
	            'schema:changed': function(msg) {

	                if(this.meaning == 'child_schema') {
	                	console.log(msg);
	                    var values = [];
	                    values.push({
	                        name: 'Select regulator',
	                        data: null
	                    });

	                    var regulators = msg.map(function(regulator) {
	                        return {
	                            name: regulator.name,
	                            data: regulator.id
	                        };
	                    });

	                    this.values = values.concat(regulators);

	                    if(regulators.length == 0) {
	                        this.visible = false;
	                    } else {
	                        this.visible = true;
	                    }
	                }
	            }
	        }, 
	        watch : {
	            values : function(val, oldVal) {
	                if(this.default != "none" || this.values.length == 1) {
	                    if(this.type == "checkbox") {
	                        this.input = [];
	                    }
	                    if(this.type == "select" && !this.meaning) {
	                        this.input = this.values[0];
	                    } else {
	                        this.input = null;
	                    }

	                    if(this.type == "radio") {
	                        this.input = this.values[0];
	                    }
	                }
	            },
	            input: function(val, oldVal) {
	                if(this.meaning == 'parent_schema') {
	                	console.log(this.input);
	                    this.$dispatch('schema:changed', this.input);
	                }
	            }
	        },
	    	methods : {
	    		getInputValues: function() {
	    			var me = this;
	    			var deferred = $.Deferred();
	    			if(this.type == "file") {
	    				var formData = new FormData();
	    				var blob = $(this.$els.input).find(":input").get(0).files[0];
	    				formData.append(this.name, blob);

	    				this.uploadFile(formData).done(function(response) {
	    					var promise = {
	    						command: me.command,
	    						name : me.name,
	    						value: response,
	                            meaning: this.meaning
	    					};
	    					deferred.resolve(promise);
	    				});

	    			} else {
	    				var promise = {
	    						command: this.command,
	    						name : this.name,
	    						value : this.input,
	                            meaning: this.meaning
						};
	    				deferred.resolve(promise);
	    			}

	    			return deferred.promise();
	    		},
	    		uploadFile: function(formData) {
	    			return $.ajax({
	    				url: "/api/file",
	    				type: "POST",
	    				data: formData,
	    				processData: false,
	    				contentType: false
	    			});
	    		}
	    	}
	    });
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
			},
			status: "idle"
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
            samplingRateField: null,
            canSwitch: true,
            status: "idle",
            mode: "experiment",
            filteredCommands: []
		},
		ready: function() {
			this.init();
		},
		events : {
			'schema:changed': function(msg) {
			    var child_schemas = _.findWhere(this.selectedExperiment.schemas, {id:msg});
			    if(child_schemas) {
			        this.$broadcast('schema:changed', child_schemas.regulators);
			    } else {
			        this.$broadcast('schema:changed', []);
			    }
			}
		},
		methods: {
			init: function() {
				var me = this;
				this.user_id = Laravel.Reservations[0].user_id;
				this.parseServers();
				this.getExperiments().done(function(response) {
	                me.physicalExperiments = _.chain(response.data).map(function(experiment) {
	                	experiment.commands = experiment.commands.data;
	                	experiment.schemas = experiment.schemas.data;
	                    _.object(_.map(experiment.commands,function(command) {
	                        return command.map(function(input) {
	                            if(input.type == "file" && input.meaning == 'parent_schema') {
	                                input.type = "select";
	                                var values = [];
	                                values.push({
	                                    name: 'Select schema',
	                                    data: null
	                                });
	                                experiment.schemas.forEach(function(schema) {
	                                    values.push({
	                                        name: schema.name,
	                                        data: schema.id
	                                    });
	                                });

	                                input.values = values;
	                            }
	                            if(input.type == "file" && input.meaning == 'child_schema') {
	                                input.type = "select";
	                                input.values = [];
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
	    			me.filteredCommands = me.selectedExperiment.experiment_commands;
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

						switch(message.event) {
							case "streaming": {
								me.status = "experimenting";
								me.series = me.formatGraphInput(
									message.data,
									me.selected.samplingRate,
									me.selectedExperiment.output_arguments
								);
								break;
							}
							case "finished": {
								me.canSwitch = true;
								break;
							}
						}
					});
				});
			},
			getExperiments: function() {
				var ids = _.chain(Laravel.Reservations).pluck('physical_device').pluck('id').value();
				return $.getJSON('api/experiments?include=commands,experiment_commands,output_arguments,schemas&physical_device=' + ids.join(','));
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
			flashSuccess: function(text) {
                noty ({
                    text : text,
                    theme: "relax",
                    layout: "topRight",
                    timeout : 5000,
                    type: 'success'
                });
            },
            collectInputData: function() {
            	var promises = [];
            	$.each(this.$children, function(index, component) {
					if($.isFunction(component.getInputValues)) {
						promises.push(component.getInputValues());
					}
				});

				return promises;
            },
            postChange: function() {
            	var me = this;
            	var promises = this.collectInputData();

            	$.when.apply($, promises).then(function() {
					var data = me.makeRequestData(arguments);
					data = {
						device : data.device,
						software: data.software,
						instance: data.instance,
						input : data.input
					};
					me.postChangeCommand(data)
					.done(function(response) {
						me.flashSuccess("Change");

					}).fail(function(response) {
                       
                    });
				});

            },
			runExperiment: function() {
				var me = this;
				var promises = this.collectInputData();

				$.when.apply($, promises).then(function() {
					var data = me.makeRequestData(arguments);
                    me.selected.samplingRate = parseInt(data.input.start[me.samplingRateField.name]);

					me.postRunExperiment(data)
					.done(function(response) {
						me.flashSuccess(response.success.message);
						me.canSwitch = false;
						me.status = "initializing";
					}).fail(function(response) {
                        response = JSON.parse(response.responseText);
                        var message = "";
                        if(typeof response.error.message == 'string') {
                        	message = response.error.message;
                        } else {
                        	_.object(_.each(response.error.message, function(field) {
                        	    message += field
                        	    message += "<br>";
                        	}));
                        }
                        
                        me.flashWarning(message);
                    });
				});
			},
			stopCommand: function() {
				var me = this;
				return $.ajax({
					"type" : "POST",
					"url" : "/api/experiments/" + me.selectedExperiment.experiment_id +"/stop",
					"data" : {
						device : me.selected.device,
						software: me.selected.software,
						instance: me.selected.instance
					}
				});
			},
			isSchemaInput: function(input) {
			    return input.meaning == 'parent_schema' || input.meaning == 'child_schema';
			},
			makeRequestData: function(inputs) {
                var me = this;
				var request = {
					device: this.selectedExperiment.device,
					software: this.selectedExperiment.software,
					input: {},
                    duration: 0,
                    sampling_rate: 0
				};

				$.each(this.filteredCommands, function(index, command) {
					request.input[command] = {};
				});
				$.each(inputs, function(index, input) {
                    var value = input.value;
                    if(me.isSchemaInput(input)) {
                        switch(input.meaning) {
                            case 'parent_schema': {
                                var schema = _.first(_.where(me.selectedExperiment.schemas,{id: input.value}));
                                if(schema) {
                                    value = schema.url;
                                }

                                break;
                            }
                            case 'child_schema': {
                                var regulator = _.first(_.chain(_.pluck(me.selectedExperiment.schemas,'regulators'))
                                    .flatten().where({id: input.value}).value());                             

                                if(regulator) {
                                    value = regulator.url;
                                }

                                break;
                            }
                        }
                    }

                    if(input.meaning == 'experiment_duration') {
                        request.duration = parseInt(value);
                    }
                    if(input.meaning == 'sampling_rate') {
                        request.sampling_rate = parseInt(value);
                    }

                    if(input.command) {
                        request.input[input.command][input.name] = value;
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
			postChangeCommand: function(data) {
				var me = this;
				return $.ajax({
					"type" : "POST",
					"url" : "/api/experiments/" + me.selectedExperiment.experiment_id +"/change",
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
			showExperiment: function() {
				this.mode = "experiment";
				this.filteredCommands = this.selectedExperiment.experiment_commands;
			},
			showChange: function() {
				this.mode = "change";
				this.filteredCommands = ["change"];
			}
		},
		computed: {
			description: function() {
				return this.selected.software + " on " + this.selected.software + " : " + this.selected.instance;
			}
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

	                var selectedExperiment = _.find(this.physicalExperiments, function(experiment) {
	                    return experiment.physical_device == me.selected.instance &&
	                    experiment.software == me.selected.software &&
	                    experiment.device == me.selected.device;
	                });

	                this.selectedExperiment.commands = selectedExperiment.commands;
	                this.selectedExperiment.experiment_commands = selectedExperiment.experiment_commands;
	                this.filteredCommands = this.selectedExperiment.experiment_commands;
	                this.showExperiment();

		        },
		        deep: true
		    }
		},
	});
})($);