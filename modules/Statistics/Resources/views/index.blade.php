@extends('user.layouts.default')

@section('content')

	<div class="row">
		<div class="col-lg-6">
			<canvas id="myChart" width="400" height="200"></canvas>
		</div>

		<div class="col-lg-6">
			<canvas id="pieChart" width="400" height="200"></canvas>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div id="map" style="height: 500px"></div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-6" id="chartdiv" style="height: 300px">
			<canvas id="barchart"></canvas>
		</div>
		<div class="col-lg-6">
			<div id="tag-cloud" class="center-block" style="height: 350px;"></div>
		</div>
	</div>







@stop

@section("page_css")
	@parent
	<link rel="stylesheet" type="text/css" href="{{ asset('css/statistics/jqcloud.css') }}">

@stop

@section("page_js")
	@parent


	<script src="{{ asset('js/statistics/Chart.min.js') }}"></script>
	<script src="{{ asset('js/statistics/Chart.bundle.min.js') }}"></script>
	<script src="{{ asset('js/statistics/jqcloud-1.0.4.min.js') }}"></script>
	<script src="{{ asset('js/statistics/amcharts.js') }}"></script>
	<script src="{{ asset('js/statistics/serial.js') }}"></script>

	<script>
		//AmCharts.themes.none={};
		var word_list = {!! json_encode($tagCloud) !!};
		$(function() {
			$("#tag-cloud").jQCloud(word_list, {shape: "rectangular"});
		});
/*
		var chart = AmCharts.makeChart("chartdiv", {
			"theme": "none",
			"type": "serial",
			"dataProvider": [{
				"date": "device1",
				"support12": 10,
				"support01": 5,
				"support02": 4,
				"support03": 2,
				"support04": 3,
				"feature12": 15,
				"feature01": 0,
				"feature02": 1,
				"feature03": 0,
				"feature04": 3,
				"priority12": 9,
				"priority01": 10,
				"priority02": 23,
				"priority03": 1,
				"priority04": 2,
				"update12": 8,
				"update01": 2,
				"update02": 3,
				"update03": 2,
				"update04": 3,
				"label": 0
			}, {
				"date": "device2",
				"support12": 10,
				"support01": 5,
				"support02": 4,
				"support03": 2,
				"support04": 3,
				"feature12": 15,
				"feature01": 0,
				"feature02": 1,
				"feature03": 0,
				"feature04": 3,
				"priority12": 9,
				"priority01": 10,
				"priority02": 23,
				"priority03": 1,
				"priority04": 2,
				"update12": 8,
				"update01": 2,
				"update02": 3,
				"update03": 2,
				"update04": 3,
				"label": 0
			}, {
				"date": "device3",
				"support12": 10,
				"support01": 5,
				"support02": 4,
				"support03": 2,
				"support04": 3,
				"feature12": 15,
				"feature01": 0,
				"feature02": 1,
				"feature03": 0,
				"feature04": 3,
				"priority12": 9,
				"priority01": 10,
				"priority02": 23,
				"priority03": 1,
				"priority04": 2,
				"update12": 8,
				"update01": 2,
				"update02": 3,
				"update03": 2,
				"update04": 3,
				"label": 0
			}],
			"valueAxes": [{
				"stackType": "regular",
				"position": "left",
				"title": "Experimenty"
			}],
			"startDuration": 1,
			"graphs": [
				// 1200 stack
				{
					"fillAlphas": 0,
					"lineAlpha": 0,
					"title": "matlab",
					"type": "column",
					"valueField": "label",
					"showAllValueLabels": true,
					"labelText": "\n[[title]]"
				},
				{
					"fillAlphas": 0.9,
					"lineAlpha": 0.2,
					"title": "1200",
					"type": "column",
					"valueField": "support12"
				}, {
					"fillAlphas": 0.9,
					"lineAlpha": 0.2,
					"title": "1200",
					"type": "column",
					"valueField": "feature12"
				}, {
					"fillAlphas": 0.9,
					"lineAlpha": 0.2,
					"title": "1200",
					"type": "column",
					"valueField": "priority12"
				}, {
					"fillAlphas": 0.9,
					"lineAlpha": 0.2,
					"title": "1200",
					"type": "column",
					"valueField": "update12"
				},
				// 0100 stack
				{
					"newStack": true,
					"fillAlphas": 0,
					"lineAlpha": 0,
					"title": "openmodelica",
					"type": "column",
					"valueField": "label",
					"showAllValueLabels": true,
					"labelText": "\n[[title]]"
				},
				{
					"fillAlphas": 0.9,
					"lineAlpha": 0.2,
					"title": "0100",
					"type": "column",
					"valueField": "support01"
				}, {
					"fillAlphas": 0.9,
					"lineAlpha": 0.2,
					"title": "0100",
					"type": "column",
					"valueField": "feature01"
				}, {
					"fillAlphas": 0.9,
					"lineAlpha": 0.2,
					"title": "0100",
					"type": "column",
					"valueField": "priority01"
				}, {
					"fillAlphas": 0.9,
					"lineAlpha": 0.2,
					"title": "0100",
					"type": "column",
					"valueField": "update01"
				},
				// 0200 stack
				{
					"newStack": true,
					"fillAlphas": 0,
					"lineAlpha": 0,
					"title": "scilab",
					"type": "column",
					"valueField": "label",
					"showAllValueLabels": true,
					"labelText": "\n[[title]]"
				},
				{
					"fillAlphas": 0.9,
					"lineAlpha": 0.2,
					"title": "0200",
					"type": "column",
					"valueField": "support02"
				}, {
					"fillAlphas": 0.9,
					"lineAlpha": 0.2,
					"title": "0200",
					"type": "column",
					"valueField": "feature02"
				}, {
					"fillAlphas": 0.9,
					"lineAlpha": 0.2,
					"title": "0200",
					"type": "column",
					"valueField": "priority02"
				}, {
					"fillAlphas": 0.9,
					"lineAlpha": 0.2,
					"title": "0200",
					"type": "column",
					"valueField": "update02"
				}
			],
			"plotAreaFillAlphas": 0.1,
			"depth3D": 30,
			"angle": 30,
			"categoryField": "date",
			"categoryAxis": {
				"labelOffset": 15,
				"gridPosition": "start",
				"tickPosition": "start"
			}
		});
*/

		var experiments = {!! json_encode($experiments) !!};

		var exp = [];
		var devLab = [];

		experiments.forEach(function(experiment){
			exp.push(experiment.total);
			devLab.push(experiment.device);
		});


		console.log(exp);
		var barData = {
			labels: devLab,
			datasets: [
				{
					label: "{{trans("statistics::default.STATISTICS_EXPERIMENTS")}}",
					backgroundColor: "rgba(255,99,132,0.2)",
					borderColor: "rgba(255,99,132,1)",
					borderWidth: 1,
					hoverBackgroundColor: "rgba(255,99,132,0.4)",
					hoverBorderColor: "rgba(255,99,132,1)",
					data: exp,
				}
			]
		};

		var ctx = $("#barchart");

		var myBarChart = new Chart(ctx, {
			type: 'bar',
			data: barData,
			options: {
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero:true
						}
					}]
				}
			}
		});


		var pieData = {
			labels: {!! json_encode($accountLabels) !!},
			datasets: [
				{
					data: {!! json_encode($accountData) !!},
					backgroundColor: [
						"#4BC0C0",
						"#F44336",
						"#3B5998",
						"#831135"
					],
					hoverBackgroundColor: [
						"#4BC0C0",
						"#F44336",
						"#3B5998",
						"#831135"
					]
				}]
		};

		var ctx2 = $("#pieChart");
		var myPieChart = new Chart(ctx2,{
			type: 'pie',
			data: pieData
		});


		var ctx = $("#myChart");

		var values = {!! json_encode($traffic) !!};


		var data = {
			labels: ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23"],
			datasets: [
				{
					label: "{!! trans("statistics::default.STATISTICS_TRAFFIC") !!}",

					// Boolean - if true fill the area under the line
					fill: false,

					// Tension - bezier curve tension of the line. Set to 0 to draw straight lines connecting points
					// Used to be called "tension" but was renamed for consistency. The old option name continues to work for compatibility.
					lineTension: 0.1,

					// String - the color to fill the area under the line with if fill is true
					backgroundColor: "rgba(75,192,192,0.4)",

					// String - Line color
					borderColor: "rgba(75,192,192,1)",

					// String - cap style of the line. See https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/lineCap
					borderCapStyle: 'butt',

					// Array - Length and spacing of dashes. See https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/setLineDash
					borderDash: [],

					// Number - Offset for line dashes. See https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/lineDashOffset
					borderDashOffset: 0.0,

					// String - line join style. See https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/lineJoin
					borderJoinStyle: 'miter',

					// The properties below allow an array to be specified to change the value of the item at the given index

					// String or Array - Point stroke color
					pointBorderColor: "rgba(75,192,192,1)",

					// String or Array - Point fill color
					pointBackgroundColor: "#fff",

					// Number or Array - Stroke width of point border
					pointBorderWidth: 1,

					// Number or Array - Radius of point when hovered
					pointHoverRadius: 5,

					// String or Array - point background color when hovered
					pointHoverBackgroundColor: "rgba(75,192,192,1)",

					// String or Array - Point border color when hovered
					pointHoverBorderColor: "rgba(220,220,220,1)",

					// Number or Array - border width of point when hovered
					pointHoverBorderWidth: 2,

					// Number or Array - the pixel size of the point shape. Can be set to 0 to not render a circle over the point
					// Used to be called "radius" but was renamed for consistency. The old option name continues to work for compatibility.
					pointRadius: 1,

					// Number or Array - the pixel size of the non-displayed point that reacts to mouse hover events
					//
					// Used to be called "hitRadius" but was renamed for consistency. The old option name continues to work for compatibility.
					pointHitRadius: 10,

					// The actual data
					data: values,

					// String - If specified, binds the dataset to a certain y-axis. If not specified, the first y-axis is used. First id is y-axis-0
					yAxisID: "y-axis-0",
					scaleStartValue: 0
				}
			]
		};

		var myLineChart = new Chart(ctx, {
			type: 'line',
			data: data,
			options: {
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero:true
						}
					}]
				}
			}
		});


		var map, heatmap;

		function initMap() {
			map = new google.maps.Map(document.getElementById('map'), {
				zoom: 3,
				center: {lat: 37.775, lng: 40.434},
				mapTypeId: google.maps.MapTypeId.TERRAIN,
				options:{
					minZoom: 2,
					maxZoom: 6
				}
			});

			heatmap = new google.maps.visualization.HeatmapLayer({
				data: getPoints(),
				map: map
			});

			heatmap.set('radius', 20);
		}


		function getPoints() {
			var data = [];
			//new google.maps.LatLng(37.791060, -122.399334),
			@foreach($items as $item)
				data.push(new google.maps.LatLng({{$item->location}}));
			@endforeach
			return data;
		}

	</script>
	<script async defer
			src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDqYpSfHr483N-c9yzrqeZ3d56BRdqHq7M&libraries=visualization&callback=initMap">
	</script>
@stop