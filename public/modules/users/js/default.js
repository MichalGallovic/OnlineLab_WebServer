//default.js
var report_chart;
var minChartHeight = 320;

Highcharts.setOptions({
	global: {
	  useUTC: false
	},
	lang: {
		resetZoom: ''   
	}
});

function get_report(reportId){
	
	$('#report_chart_container').html('');
	$('#report_id').html('');
	$('#console_box .box-content').html('');
	$('#regulator_settings').html('');
	$('#regulator').html('');
	$('#equipment_name').html('');
	$('#report_date').html('');
	
	$('#report_container').slideUp(function(){
		$('#pager_holder').hide();
		$('#buttons').show();
		$('#report_chart_box').slideDown(function(){
			$('#chart_ajax_loader').show();
			$.post(ROOT_PATH + "includes/modules/report/ajax.php",{"getReport":1,"reportId":reportId}, function(data) {
				//buttony
				$('.settings_button').show();
				$('.csv_button').show();
				$('.xml_button').show();
				$('.json_button').show();
				
				$('#report_id').html(data.reportId);
				$('#console_box .box-content').html(data.console);
				$('#regulator_settings').html(data.regulator_settings);
				$('#regulator').html(data.regulator);
				$('#equipment_name').html(data.equipment_name);
				$('#report_date').html(data.report_date);
				$('input[name="reportId"]').val(reportId);
				
				$('#report_time').html(data.report_simulation_time);
			
				
				$('#report_input_value').html(data.experiment_settings.input);
				$('#ts').html(data.experiment_settings.ts);
				$('#out_value').html(data.experiment_settings.out_value);
				$('#in_value').html(data.experiment_settings.in_value);
				if(data.notes != '')
					$('#personal_notes_box textarea').val(data.notes);
				else
					$('#personal_notes_box textarea').val(personalNotesDefaultValue);	
				
				//-------------- KONZOLA VYSTUP ----------------
				if(data.box_settings.console_box == 1){
					$('#console_box').show();
				}
				
				//-------VSTUPNE NASTAVENIA EXPERIMENTU BOX -----
				if(data.box_settings.input_experiment_settings_box == 1){
					$('#input_experiment_settings_box').show();
				}
				
				//----------------OSOBNE POZNAMKY-------------
				if(data.box_settings.personal_notes_box == 1){
					$('#personal_notes_box').show();
				}
					
				if(data.experiment_settings.c_lamp){
					$('#c_lamp').html(data.experiment_settings.c_lamp);
					$('#c_lamp_info').show();
				}
				if(data.experiment_settings.c_fan){
					$('#c_fan').html(data.experiment_settings.c_fan);
					$('#c_fan_info').show();
				}
				if(data.experiment_settings.c_led){
					$('#c_led').html(data.experiment_settings.c_led);
					$('#c_led_info').show();
				}
				
				if(data.nextReport){
					$('#next_report').attr('rel',data.nextReport);
					$('#next_report').css('display','inline-block');
				}
				
				if(data.previousReport){
					$('#previous_report').attr('rel',data.previousReport);
					$('#previous_report').css('display','inline-block');
				}
				
				renderReportChart(data.chartData);
			},"json");
			//var t=setTimeout("renderReportChart()",2000);
			
		});
	});
}

//funkcia pre dalsi a predchadzajuci report
function show_report(reportId){
	$('#report_chart_container').html('');
	$('#report_id').html('');
	$('#next_report').hide();
	$('#previous_report').hide();
	//$('#report_chart_box').hide();
	$('#console_box').hide();
	$('#input_experiment_settings_box').hide();
	$('#personal_notes_box').hide();
	$('#c_led_info').hide();
	$('#c_lamp_info').hide();
	$('#c_fan_info').hide();
	$('.settings_button').hide();
	$('.csv_button').hide();
	$('.xml_button').hide();
	$('.json_button').hide();
	
	
	$('#report_chart_box').slideDown(function(){
		$('#chart_ajax_loader').hide();										  
		$('.dashboard_overlay').show();
		$.post(ROOT_PATH + "includes/modules/report/ajax.php",{"showReport":1,"reportId":reportId}, function(data){
			$('.settings_button').show();
			$('.csv_button').show();
			$('.xml_button').show();
			$('.json_button').show();
			
			$('#report_id').html(data.reportId);
			$('#console_box .box-content').html(data.console);
			$('#regulator_settings').html(data.regulator_settings);
			$('#regulator').html(data.regulator);
			$('#equipment_name').html(data.equipment_name);
			$('#report_date').html(data.report_date);
			$('input[name="reportId"]').val(reportId);
			$('#report_time').html(data.report_simulation_time);
			$('#report_input_value').html(data.experiment_settings.input);
			$('#ts').html(data.experiment_settings.ts);
			$('#out_value').html(data.experiment_settings.out_value);
			$('#in_value').html(data.experiment_settings.in_value);
			if(data.notes != '')
				$('#personal_notes_box textarea').val(data.notes);
			else
				$('#personal_notes_box textarea').val(personalNotesDefaultValue);	
			
			if(data.nextReport && reportId != data.nextReport ){
				$('#next_report').attr('rel',data.nextReport);
				$('#next_report').css('display','inline-block');
			}
			
			if(data.previousReport && reportId != data.previousReport ){
				$('#previous_report').attr('rel',data.previousReport);
				$('#previous_report').css('display','inline-block');
			}
			
			//-------------- KONZOLA VYSTUP ----------------
			if(data.box_settings.console_box == 1){
				$('#console_box').show();
			}
			
			//-------VSTUPNE NASTAVENIA EXPERIMENTU BOX -----
			if(data.box_settings.input_experiment_settings_box == 1){
				$('#input_experiment_settings_box').show();
			}
			
			//----------------OSOBNE POZNAMKY-------------
			if(data.box_settings.personal_notes_box == 1){
				$('#personal_notes_box').show();
			}
			
			if(data.experiment_settings.c_lamp){
				$('#c_lamp').html(data.experiment_settings.c_lamp);
				$('#c_lamp_info').show();
			}
			if(data.experiment_settings.c_fan){
				$('#c_fan').html(data.experiment_settings.c_fan);
				$('#c_fan_info').show();
			}
			if(data.experiment_settings.c_led){
				$('#c_led').html(data.experiment_settings.c_led);
				$('#c_led_info').show();
			}
			
			$('.dashboard_overlay').hide();
			renderReportChart(data.chartData);
		},"json");
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

function randomData(){
	var time = (new Date()).getTime(),i;
	var tempPointsArray = [];
	
	for (i = -9; i <= 0; i++) {
	   tempPointsArray.push({
		  x: time + i * 1000,
		  y: randomFromTo(-4,4)
	   });
	}
	
	return tempPointsArray;	
}

function randomFromTo(from, to){
   return Math.floor(Math.random() * (to - from + 1) + from);
}

function renderReportChart(data){
	
	
	report_chart = new Highcharts.Chart({
		chart: {
			renderTo: 'report_chart_container',
			defaultSeriesType: 'spline',
			events: {
				//load: requestData,
				selection: function(event) {				 
					report_chart.xAxis[0].setExtremes(event.xAxis[0].min, event.xAxis[0].max);
					report_chart.yAxis[0].setExtremes(event.yAxis[0].min, event.yAxis[0].max);
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


$(document).ready(function(){  
	
	if($('.report_info_box').length > 0){
		$('.report_info_box').delay(2500).fadeOut();
	}
	
	$(".settings_button").click(function(){
			
			//zistenie velkosti okna s grafom
			var chartDivHeight = $('#report_chart_box').height();
			
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
	
     // zavretie nastavenie grafu	
	 $(".close-panel-btn").click(function(){
	 	$(".chart-overlay").removeClass('active');
		$(".chart-overlay").fadeOut();
		$(".panel").toggle("fast");
		return false;
	 });
	
	//hanlder pre button reset
	$('#reset-zoom').click(function(){
		report_chart.xAxis[0].setExtremes(null, null);
		report_chart.yAxis[0].setExtremes(null, null);
	});
	
	//hanlder pre button zoo1
	$('#zoom-1').click(function(){
		var Xextremes = report_chart.xAxis[0].getExtremes();
		var Yextremes = report_chart.yAxis[0].getExtremes();
		var XdataMin = Xextremes.dataMin;
		var XdataMax = Xextremes.dataMax;
		var YdataMin = Yextremes.dataMin;
		var YdataMax = Yextremes.dataMax;
	
		report_chart.xAxis[0].setExtremes(XdataMin, XdataMax);
		report_chart.yAxis[0].setExtremes(YdataMin, YdataMax);
	});
	
	//hanlder pre button zoom2
	$('#zoom-2').click(function(){
		var extremes = report_chart.xAxis[0].getExtremes();
		var dataMin = extremes.dataMin;
		var dataMax = extremes.dataMax;
		var rozdiel = dataMax - dataMin; 
		var zoom = Math.round(rozdiel/4);
	
		report_chart.xAxis[0].setExtremes(dataMin+zoom, dataMax-zoom);
			
	});
	
	//hanlder pre button dopredu
	$('#forward').click(function(){
		var extremes = report_chart.xAxis[0].getExtremes();
		var Max = extremes.max;
		var Min = extremes.min;
		var newMax = Math.round(Max/2);
		var newMin = Math.round(Min/2);
		var rozdiel = newMax - newMin; 
		var zoom = Math.round(rozdiel/2);
		
		report_chart.xAxis[0].setExtremes(Min+zoom, Max+zoom);
	});
	
	
	
	//hanlder pre button dozadu
	$('#backward').click(function(){
		var extremes = report_chart.xAxis[0].getExtremes();
		var Max = extremes.max;
		var Min = extremes.min;
		var newMax = Math.round(Max/2);
		var newMin = Math.round(Min/2);
		var rozdiel = newMax - newMin; 
		var zoom = Math.round(rozdiel/2);
		
		report_chart.xAxis[0].setExtremes(Min-zoom, Max-zoom);
	});
	
	$('#back_to_reports').click(function(){
		
	    $('#console_box .box-content').html('');
		$('#console_box').hide();
		$('#input_experiment_settings_box').hide();
		$('#personal_notes_box').hide();
		$('#report_chart_box').slideUp(function(){
			$('#buttons').hide();
			$('#report_container').slideDown();
			$('#pager_holder').show();
		});	
	});
	
	
	$('#personal_notes_box textarea').click(function(){
		var valueString = $.trim($(this).val());
		if(valueString === personalNotesDefaultValue){
			valueString = '';	
			this.value = '';
		}
		$(this).caret(valueString.length);
	});
	
	$('#personal_notes_box textarea').blur(function(){
		var valueString = $.trim($(this).val());
		if(valueString === ''){
			this.value = personalNotesDefaultValue;
		}											
	});
	
	$('#personal_notes_box textarea').change(function(){
		var valueString = $.trim($(this).val());
		var report_id = $('#report_id').text();
		if(valueString === personalNotesDefaultValue){
			valueString = '';	
			this.value = '';
		}else
			$.post(ROOT_PATH + "includes/modules/report/ajax.php",{"saveNotes":1,"notes":valueString,"report_id" : report_id }, function(data){});
	});
	
	$('#previous_report').click(function(){
		show_report($(this).attr('rel'));
	});
	
	$('#next_report').click(function(){
		show_report($(this).attr('rel'));
	});
	
	
	//reports options..
	$("#reports-open-options").click(function(){
		$("#reports-boxes-wrapper").slideDown("slow");
		$('#button_stop_sim').show();
								$('#stop-continue-btn').show();
	});	
	
	$("#reports-close-options").click(function(){
		$("#reports-boxes-wrapper").slideUp("slow");	
	});
	
	$('#reports-boxes-btn-wrapper a').click(function () {
		$("#reports-boxes-btn-wrapper a").toggle();
	});
	
	//ak klikneme na jeden z checkboxov..
	$('#reports-boxes-wrapper input[type="checkbox"]').click(function(){
		var boxIdName = $(this).attr('name');	
		var consoleContent = $('#console_box .box-content').html();
		//ak sme ho prave zaskrtli
		if($(this).is(':checked')){
			if(consoleContent !== '')
				$('#' + boxIdName).show();
			$.post(ROOT_PATH + "includes/modules/report/ajax.php",{"setReportBox":1,"box":boxIdName}, function(data) {
			});
		}else{
			if(consoleContent !== '')
				$('#' + boxIdName).hide();
			$.post(ROOT_PATH + "includes/modules/report/ajax.php",{"unsetReportBox":1,"box":boxIdName}, function(data) {
			});
		}
																  
	});
	
	
});