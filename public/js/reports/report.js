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
		el: "#report-app",
		data: {
			report: null,
			series: null,
			description: null
		},
		ready: function() {
			this.report = this.formatReport(Laravel.Report);
			this.description = this.report.device + " " + this.report.software + " on " + this.report.physical_device;
			this.series = this.formatGraphInput(
				this.report.output,
				this.report.measuring_rate,
				this.report.output_arguments
			);
			$('#editor').summernote({
				height: 100
			});
		},
		methods: {
			update: function() {
				var me = this;
				var notes = $('#editor').summernote('code');
				$.ajax({
					type: "POST",
					data: {
						notes: notes
					}
				}).done(function(response) {
					me.flashSuccess(response.success);
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
			formatReport: function(report) {
				var input_arguments = report.input_arguments;
				_.object(_.map(input_arguments, function(command, commandName) {
					_.object(_.map(command, function(input, key) {
						input.data = report.input[commandName][input.name];
						return input;
					}));
					return command;
				}));

				report.input = input_arguments;
				return report;
			},
			formatGraphInput: function(data, rate, output_arguments) {
				var me = this;
				var series = [];
				var indexCounter = 0;
				$.each(data, function(index, measurement) {
					var measurementWithTime = [];
					$.each(measurement, function(indexCounter, value) {
						measurementWithTime.push([indexCounter*rate, value]);
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
		}
	});
})($);