Vue.config.devtools = true;

!(function() {
	$.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() }
    });
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
	var vm = new Vue({
		"el" : "#queueApp",
		data: {
			"selectedExperiment" : null,
			"experiments" : null,
            "physicalExperiments": null,
            selected : {
                instance : null,
                software: null,
                device: null
            }
		},
		ready: function() {
			var me = this;
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
			});
		},
        watch: {
            selectedExperiment: function(newSelection, oldVal) {
                if(Laravel.user.role == 'admin') {
                    this.selected.instance = this.selectedExperiment.instances[0].name;
                    this.selected.device = this.selectedExperiment.device;
                    this.selected.software = this.selectedExperiment.software;
                } else {
                    if(this.selectedExperiment.instances.length == 1) {
                        this.selected.instance = this.selectedExperiment.instances[0].name;
                        this.selected.software = this.selectedExperiment.software;
                        this.selected.device = this.selectedExperiment.device;
                    } else {
                        this.selected = {
                            instance: null
                        }
                    }
                }
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
		methods : {
            flashSuccess: function(text) {
                noty ({
                    text : text,
                    theme: "relax",
                    layout: "topRight",
                    timeout : 5000,
                    type: 'success'
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

					me.postQueueExperiment(data)
					.done(function(response) {
						me.flashSuccess(response.success.message);
					}).fail(function(response) {
                        response = JSON.parse(response.responseText);
                        var message = "";
                        _.object(_.each(response.error.message, function(field) {
                            message += field
                            message += "<br>";
                        }));
                        
                        me.flashWarning(message);
                    });
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

				$.each(this.selectedExperiment.experiment_commands, function(index, command) {
					request.input[command] = {};
				});
				$.each(inputs, function(index, input) {
                    var value = input.value;
                    console.log(input);
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
			getExperiments: function() {
				return $.getJSON("/api/experiments?include=commands,experiment_commands,schemas");
			},
			postQueueExperiment: function(data) {
				var me = this;
				return $.ajax({
					"type" : "POST",
					"url" : "/api/experiments/" + me.selectedExperiment.experiment_id +"/queue",
					"data" : data
				});
			}
		},
        events: {
            'schema:changed': function(msg) {
                var child_schemas = _.findWhere(this.selectedExperiment.schemas, {id:msg});
                if(child_schemas) {
                    this.$broadcast('schema:changed', child_schemas.regulators);
                } else {
                    this.$broadcast('schema:changed', []);
                }
            }
        }
	});
})();