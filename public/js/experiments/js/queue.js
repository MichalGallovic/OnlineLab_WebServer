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
            default: null
    	},
    	data: function() {
    		return {
    			input : null
    		}
    	},
    	ready: function() {

    	},
        watch : {
            values : function(val, oldVal) {
                if(this.default != "none" || this.values.length == 1) {
                    if(this.type == "checkbox") {
                        this.input = [];
                    }
                    if(this.type == "select") {
                        this.input = this.values[0];
                    }

                    if(this.type == "radio") {
                        this.input = this.values[0];
                    }
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
    						value: response
    					};
    					deferred.resolve(promise);
    				});

    			} else {
    				var promise = {
    						command: this.command,
    						name : this.name,
    						value : this.input
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
			"experiments" : null
		},
		ready: function() {
			var me = this;
			this.getExperiments().done(function(response) {

				me.experiments = response.data.map(function(experiment) {
					experiment.commands = experiment.commands.data;
					experiment.experiment_commands = experiment.experiment_commands.data;
					return experiment;
				});
				me.selectedExperiment = me.experiments[0]
			});
		},
		methods : {
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
						console.log(response);
					});
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
                    } else {
                        if(input.name == "instance") {
                            request.instance = input.value;
                        }
                    }
				});

				return request;
			},
			getExperiments: function() {
				return $.getJSON("/api/experiments?include=commands,experiment_commands");
			},
			postQueueExperiment: function(data) {
				var me = this;
				return $.ajax({
					"type" : "POST",
					"url" : "/api/experiments/" + me.selectedExperiment.id +"/queue",
					"data" : data
				});
			}
		}
	});
})();