//gobal variable
var isPause = true, 
	minChartHeight = 320,
	posun = 0;

			
Highcharts.setOptions({
   global: {
      useUTC: false
   },
   lang: {
		resetZoom: ''   
   }
});

var intenzitaLabel = 'Sveteľná intenzita';
	var inputLabel = 'Vstup';
	var tempLabel = 'Teplota';
	var filTempLabel = 'Filtrovaná teplota';
	var filIntLabel = 'Filtrovaná sveteľná intenzita';
	var currnetLabel = 'Prúd';
	var rpmLabel = 'Otáčky ventilátora RPM';

$(document).ready(function() {
	 /*$("#chart_container").load(ROOT_PATH + "includes/modules/livechart/ajax.php?action=getLastReport",function(){
		
	});*/
	 
	 $.post(ROOT_PATH + "includes/modules/livechart/ajax.php",{action: "getLastReport"} ,function(data) {
	  	$("#chart_container").html(data.html);
		if(data.chartData.length <= 0)
			$("#intro").html(data.chartIntro);
		else{
			renderLasrReportChart(data.chartData);
			$('#livechart-modul .header ').append('<span id="last-measurment-title">'+data.title+'</span>');
		}
	  },"json");
	
	 $(".settings_button").click(function(){
			
			//zistenie velkosti okna s grafom
			var chartDivHeight = $('#livechart-modul').height();
			
			//alert( $('#livechart-modul').height());
			
			if(chartDivHeight < minChartHeight){
				$("#panel-menu").dialog({
					autoOpen: false,
					width: 500,
					modal: true,
					position: ['center', 'center'],
					draggable: false,
					dialogClass: 'default-dialog'
				});
				$("#panel-menu").dialog("open");
			}else{
				if($(".chart-overlay").hasClass('active')){
					$(".chart-overlay").removeClass('active');
					$(".chart-overlay").fadeOut();
				}else{
					$(".chart-overlay").addClass('active');
					$(".chart-overlay").fadeIn();	
				}
				$(".panel").toggle("fast");
				$(this).toggleClass("active");	
			}
	        return false;
	 });
	 
	$(".close-panel-btn").click(function(){
	 	$(".chart-overlay").removeClass('active');
		$(".chart-overlay").fadeOut();
		$(".panel").toggle("fast");
		return false;
	 });
	
	
	
	/*handlery pre buttony*/
	
	
	//handler pre stopovanie a pokracovanie v merani 
	$('#stop-continue-btn').click(function(){
		if (isPauseForChartDrawing) {
			alert('Eexperiment bude pozastavený');
			$('#info-chart').show().delay(1500).fadeOut(400);
			$('#stop-continue-btn span#stop-measurement').hide();
			$('#stop-continue-btn span#continue-measurement').show();
		} else {
			$('#info-chart').fadeOut();
			//vymazame staru sadu dat 
			
			/*
			chart.series[0].remove();
			chart.series[1].remove();
			chart.series[2].remove();
			chart.series[3].remove();
			chart.series[4].remove();
			chart.series[5].remove();*/
			
			//vytvorime novu aj s datami nazbieranymi pocas zastaveni grafu
			/*chart.addSeries[{
								name: "Sveteľná intenzita",
								color: '#7a5c01',
								data: tempPointsArray ,
								marker: {
									enabled: false,
									states: {
										hover: {
											enabled: true
										}
									}
								}
							},
							{
								name: "Vstup",
								color: '#0516f7',
								data: tempInputPointsArray ,
								marker: {
									enabled: false,
									states: {
										hover: {
											enabled: true
										}
									}
								}
							},
							{
								name: 'Teplota',
								color: '#fa0000',
								data: tempTemperatureArray,
								marker: {
									symbol: 'circle',
									enabled: false,
									states: {
										hover: {
											enabled: true
										}
									}
								}
							},
							{
								name: 'Filtrovaná teplota',
								color: '#fdb1b1',
								data: tempFilterTemperatureArray,
								marker: {
									symbol: 'circle',
									enabled: false,
									states: {
										hover: {
											enabled: true
										}
									}
								}
							},
							{
								name: 'Filtrovaná sveteľná intenzita',
								color: '#EDC240',
								data: tempFilterIntenzitaArray,
								marker: {
									symbol: 'circle',
									enabled: false,
									states: {
										hover: {
											enabled: true
										}
									}
								}
							},
							{
								name: 'Prúd',
								color: '#fff600',
								data: tempCurrentArray,
								marker: {
									symbol: 'circle',
									enabled: false,
									states: {
										hover: {
											enabled: true
										}
									}
								}
							}
							];
			
			*/
			chart.redraw();
			$('#stop-continue-btn span#continue-measurement').hide();
			$('#stop-continue-btn span#stop-measurement').show();
		}
		isPauseForChartDrawing = !isPauseForChartDrawing
	});
	
	$('#button_stop_sim').click(function(){
		f=document.formular;
		
		$('#stop-chart').show();
		$('#stop-continue-btn span#stop-measurement').show();
		$.ajax({
			type:	"POST",
			url:	ROOT_PATH + "includes/modules/experiment/ajax_experiment_gate_stop.php",
			data:	"terminate=1&plant_id="+f.plant_id.value+"&gate_session="+gate_session,
			success: 
				function(html){
					$('#stop-chart').delay(1500).fadeOut(400);
				}
		});
	});
	
	//hanlder pre button reset
	$('#reset-zoom').click(function(){
		chart.xAxis[0].setExtremes(null, null);
		chart.yAxis[0].setExtremes(null, null);
	});
	
	//hanlder pre button zoo1
	$('#zoom-1').click(function(){
		var Xextremes = chart.xAxis[0].getExtremes();
		var Yextremes = chart.yAxis[0].getExtremes();
		var XdataMin = Xextremes.dataMin;
		var XdataMax = Xextremes.dataMax;
		var YdataMin = Yextremes.dataMin;
		var YdataMax = Yextremes.dataMax;
	
		chart.xAxis[0].setExtremes(XdataMin, XdataMax);
		chart.yAxis[0].setExtremes(YdataMin, YdataMax);
	});
	
	//hanlder pre button zoom2
	$('#zoom-2').click(function(){
		var extremes = chart.xAxis[0].getExtremes();
		var dataMin = extremes.dataMin;
		var dataMax = extremes.dataMax;
		var rozdiel = dataMax - dataMin; 
		var zoom = Math.round(rozdiel/4);
	
		chart.xAxis[0].setExtremes(dataMin+zoom, dataMax-zoom);
			
	});
	
	//hanlder pre button dopredu
	$('#forward').click(function(){
		var extremes = chart.xAxis[0].getExtremes();
		var Max = extremes.max;
		var Min = extremes.min;
		var newMax = Math.round(Max/2);
		var newMin = Math.round(Min/2);
		var rozdiel = newMax - newMin; 
		var zoom = Math.round(rozdiel/2);
		
		chart.xAxis[0].setExtremes(Min+zoom, Max+zoom);
	});
	
	//hanlder pre button dopredu
	$('#up').click(function(){
		var extremes = chart.yAxis[0].getExtremes();
		var dataMax = extremes.max;
		var dataMin = extremes.min;
		if(posun == 0)
			posun = roundNumber(((dataMax - dataMin) / 4),2);
		
		chart.yAxis[0].setExtremes(dataMin - posun, dataMax - posun);
	});
	
	
	//hanlder pre button dopredu
	$('#down').click(function(){
		var extremes = chart.yAxis[0].getExtremes();
		var dataMax = extremes.max;
		var dataMin = extremes.min;
		if(posun == 0)
			posun = roundNumber(((dataMax - dataMin) / 4),2);
		alert(posun);
		chart.yAxis[0].setExtremes(dataMin + posun, dataMax + posun);
	});
	
	//hanlder pre button dozadu
	$('#backward').click(function(){
		var extremes = chart.xAxis[0].getExtremes();
		var Max = extremes.max;
		var Min = extremes.min;
		var newMax = Math.round(Max/2);
		var newMin = Math.round(Min/2);
		var rozdiel = newMax - newMin; 
		var zoom = Math.round(rozdiel/2);
		
		chart.xAxis[0].setExtremes(Min-zoom, Max-zoom);
	});
});





//needed functions
function randomFromTo(from, to){
   return Math.floor(Math.random() * (to - from + 1) + from);
}

function randomData(){
	var time = (new Date()).getTime(),i;
				
	for (i = -9; i <= 0; i++) {
	   tempPointsArray.push({
		  x: time + i * 100000,
		  y: randomFromTo(-4,4)
	   });
	}
	
	return tempPointsArray;	
}

function requestData() {
    $.ajax({
        url: ROOT_PATH + 'includes/modules/livechart/ajax/live-server-data.php',
        success: function(point) {
            
			var series = chart.series[0], i; 
			
			//zbierame vsetky body
			tempPointsArray.push(point);
			
			if(isPause){
				chart.series[0].addPoint(point, true, false);	
			}
			 
		},
        cache: true
    });
}

function open_menu(){

	//formular zobrazujuci sa v dialogovom okne
	$("#panel-menu").dialog({
		autoOpen: false,
		width: 500,
		modal: true,
		position: ['center', 'center'],
		draggable: false,
		dialogClass: 'default-dialog'
		//buttons: {}
		
	});
	
	//zavreme okno s tanstaveniami v grafe
	$(".chart-overlay").removeClass('active');
	$(".chart-overlay").fadeOut();
	$(".panel").toggle("fast");
	
	$("#panel-menu").dialog("open");
	
	$(".panelMenuCLass .ui-dialog-titlebar-close").show();
}

function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}

function renderLasrReportChart(data){
	/*var data2 = new Object;
	var d1 = [];
	var d2 = [];
	var d3 = [];
	
	
	d2.push({x:0,y:0});
	d2.push({x:1,y:0});
	d2.push({x:2,y:0});
	d2.push({x:3,y:0});
	d2.push({x:4,y:30});
	d2.push({x:5,y:30});
	d2.push({x:6,y:30});
	d2.push({x:7,y:30});
	d2.push({x:8,y:30});
	
	
	d1.push({x:0,y:0});
	d1.push({x:1,y:0});
	d1.push({x:3,y:0});
	d1.push({x:4,y:0});
	d1.push({x:5,y:0});
	d1.push({x:6,y:39});
	d1.push({x:7,y:22});
	d1.push({x:8,y:35});
	
	for (i = 9; i <= 40; i++) {
	   d1.push({
		  x: i,
		  y: randomFromTo(29.8,30.2)
	   });
	   d2.push({x:i,y:30});
	   d3.push({x:i,y:i*0.25});
	}
	
	for (i = 0; i <= 40; i++) {
	   d3.push({x:i,y:i*0.25});
	}
	
	data2.intenzita = d1;
	data2.input = d2;
	data2.f_temperature = d3;*/
	
	
	$('.settings_button').show();
	chart = new Highcharts.Chart({
		chart: {
			renderTo: 'chart_container',
			defaultSeriesType: 'spline',
			events: {
				//load: requestData,
				selection: function(event) {				 
					chart.xAxis[0].setExtremes(event.xAxis[0].min, event.xAxis[0].max);
					chart.yAxis[0].setExtremes(event.yAxis[0].min, event.yAxis[0].max);
				}
			},
			zoomType: 'xy' //na xy
		},
		
		credits: {
			enabled: false
		},
		tooltip: {
			formatter: function() {
					return '<b>'+ this.series.name +'</b> <br/>'+
					'Čas:' + this.x +'s<br/>Hodnota:'+ this.y;
			}
		},
		series: [{
					name: intenzitaLabel,
					color: '#7a5c01',
					data: data.intenzita,
					marker: {
						symbol: 'circle',
						enabled: false,
						states: {
							hover: {
								enabled: true
							}
						}
					}
				},
				{
					name: inputLabel,
					color: '#0516f7',
					data: data.input,
					marker: {
						symbol: 'circle',
						enabled: false,
						states: {
							hover: {
								enabled: true
							}
						}
					}
				},
				{
					name: tempLabel,
					color: '#fa0000',
					data: data.temperature,
					visible: false,
					marker: {
						symbol: 'circle',
						enabled: false,
						states: {
							hover: {
								enabled: true
							}
						}
					}
				},
				{
					name: filTempLabel,
					color: '#fdb1b1',
					data: data.f_temperature,
					visible: false,
					marker: {
						symbol: 'circle',
						enabled: false,
						states: {
							hover: {
								enabled: true
							}
						}
					}
				},
				{
					name: filIntLabel,
					color: '#EDC240',
					visible: false,
					data: data.f_intenzita,
					marker: {
						symbol: 'circle',
						enabled: false,
						states: {
							hover: {
								enabled: true
							}
						}
					}
				},
				{
					name: currnetLabel,
					color: '#fff600',
					data: data.current,
					visible: false,
					marker: {
						symbol: 'circle',
						enabled: false,
						states: {
							hover: {
								enabled: true
							}
						}
					}
				},
				{
					name: rpmLabel,
					color: '#cc00ff',
					data: data.rotaion,
					visible: false,
					marker: {
						symbol: 'circle',
						enabled: false,
						states: {
							hover: {
								enabled: true
							}
						}
					}
				}]
	});
}

