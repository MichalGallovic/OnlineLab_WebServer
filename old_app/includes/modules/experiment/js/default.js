/*globalne premenne*/
var step=100000;
var finish=1;
var error_accoured=false;
var i=0;
var started=0;
var simend=200;
var old_input;
var indata="";
var jresp="";
var maximum=8;
var msg="";
var msg_prefix="";
var gate_session="";
var sess_backup="";
var f;
var plotdata = new Array();
var d1 = []; var d2= []; var d3= []; var d4= []; var d5= []; var d6= []; var d7= [];

var equipment;
var render_graf = 1; // premenna ktora mi urci ci do grafu pridat bod alebo ho iba nacitat.
var seriesObject = new Object;

function empty() {this.splice(0,this.length);}	//pridame metodu vyprazdnenia pre triedu Array
Array.prototype.empty=empty;
function contains(it) { return this.indexOf(it) != -1; }
String.prototype.contains = contains;

$(document).ready(function(){
	
	var noAvaibleWindow = $("#no-avaible-window");
	
	$("a.unavaible").click(function(){
	 	noAvaibleWindow.dialog({
			width: 400,
			dialogClass: "info-dialog",
			modal: true,
			position: ['center','center']								   
		}); 
		
		$(".info-dialog-close-btn").click(function(){
			noAvaibleWindow.dialog("close");									   	
		});
			
	});
	
	$("a.avaible").click(function(){
	 	equipment =  $(this).attr("rel");
		
		$("#" + equipment).dialog({
			width: 700,
			dialogClass: "info-dialog",
			modal: true,
			position: ['center','center']								   
		}); 
		
		$(".info-dialog-close-btn").click(function(){
			$("#" + equipment).dialog("close");									   	
		});
	});
	
	
	$("#num_of_tanks").change(function (){
		//pozadovany stav nadob
		var num_of_tanks = $("#num_of_tanks option:selected").val();
		//aktualny stav nadob
		var current_num_of_tank = $(".tank").size();
		
		$("table.settings tbody").html('');
		
		for(var i=1; i <= num_of_tanks; i++ ){
			$("table.settings tbody").append("<tr class='tank'><td><label for='vstup_" + i + "'>Výška hladiny v " + i + ".stĺci</label></td><td><input id='vstup_" + i + "' type='text' name='vstup[" + i + "]'  value='10' /></td> </tr>");
		}
	});
	
	//termo napatie selectbox
	$("#vstup_switch").change(function(){
		var c_vst_number = $("#vstup_switch option:selected").val();	
		
		$('#c_vst_1, #c_vst_2, #c_vst_3').show();
		$('#c_vst_' +  c_vst_number).hide();
	});
	
	//termo checkboxy
	$("#avaible-for-termo input[name=ctrl_typ]").change( function(){
		var reg_typ =  $("#avaible-for-termo input[name='ctrl_typ']:checked").val().toLowerCase();
		$('#avaible-for-termo .own_reg,#avaible-for-termo .no_reg,#avaible-for-termo .pid_reg').fadeOut().delay(600);
		$('#avaible-for-termo .' + reg_typ + '_reg').fadeIn();	
	});
	
	//hydro checkboxy
	$("#avaible-for-hydro input[name=ctrl_typ]").change( function(){
		var reg_typ =  $("#avaible-for-hydro input[name='ctrl_typ']:checked").val().toLowerCase();
		$('#avaible-for-hydro .own_reg,#avaible-for-hydro .no_reg,#avaible-for-hydro .pid_reg').fadeOut().delay(600);
		$('#avaible-for-hydro .' + reg_typ + '_reg').fadeIn();	
	});
	
	$(window).bind('beforeunload', function(){return "Ste si istý, že chcete opustiť stránku?\n Ak máte spustený experiment, tak bude ukončený.";});
	$(window).unload(function(){
		if(!finish){
			finish = true;
			$('#stop-chart').show();
			$('#stop-continue-btn span#stop-measurement').show();
			$.ajax({
				type:	"POST",
				url:	ROOT_PATH + "includes/modules/experiment/ajax_experiment_gate_stop.php",
				data:	"terminate=1&plant_id=0&gate_session="+gate_session,
				success: 
					function(html){
						$('#stop-chart').fadeOut(400);
					}
			});
		}
	});
	
	//-----------------------------------------------------------------------
	//***********************************************************************
	//-----------------------------------------------------------------------
	
	$("#termo").click(function(){
		simuluj();
		
	});
	
});


/*funkcie pre napojenie sa na relane zariadene a zacanie simulacie*/
function simuluj(){
	start_req();
	//a nasledne spustime funkciu na rtm citanie udajov
	read_req();
}

function start_req(){
	f=document.formular;
	//nastavim pociatocne podmienky
	exp_init();
	//zavrem okno
	$("#console").html("<b>Stav:</b> simulácia sa začína...");
	$("#" + equipment).dialog("close");
	//loadovanie- zaciatok
	$('#chart_ajax_loader').show();
	
	var postString = "vstup="+f.vstup.value+
					 "&in_sw="+f.vstup_switch.value+
					 "&out_sw="+f.vyst_switch.value+
					 "&c_lamp="+f.c_lamp.value+
					 "&c_led="+f.c_led.value+
					 "&c_fan="+f.c_fan.value+
					 "&time="+f.time.value+
					 "&ts="+f.sample_time.value+
					 "&P="+f.P.value+
					 "&I="+f.I.value+
					 "&D="+f.D.value+
					 "&OWN="+(f.ctrl_typ[2].checked ? "2" : (f.ctrl_typ[1].checked ? "1" : "0"))+
					 "&scifun="+encodeURIComponent(f.own_func.value)+
					 //v nasledovnom riadku otestujeme ci bola vykonana nejaka zmena v tele regulatora alebo nie. Ak nie, tak ulozime id.
					 "&own_ctrl_id=-1&plant_id="+f.plant_id.value+"&gate_session="+gate_session;
	
	$.post(ROOT_PATH + "includes/modules/experiment/model_runner_0.php",postString,
		   function(data){
		   		if(data.error == 1){
					error_accoured = true;
					alert(data.msg);
					$('#chart_ajax_loader').show();
				}
				
				if(data.error == 0){
					$('#console').append("<b>Stav:</b> "+data.output);	
				}
		   
		   },"json"
	);
}

function read_req(){
	var f = document.formular;
	
	if(!finish){
		$.ajax({
			type: "POST",
			url: ROOT_PATH + "includes/modules/experiment/ajax_experiment_gate_rtm_read.php",
			dataType: "text",
			cache: false,
			data: "plant_id=0&gate_session="+gate_session,
			async: false,
			success: 
				function(resp){
					//alert(resp);
					if(!started && !error_accoured){started=1;}
					if(IsJsonString(resp)){
						d1= []; d2= []; d3= []; d4= []; d5= []; d6= []; d7= [];
						seriesObject = {};
						jresp=JSON.parse(resp);
						for(j=0; j<jresp.length; j++ ){
							if(jresp[j].done == "true"){
								msg = msg + jresp[j].sur+"<br />";finish=true;
							}else{
								//$('.ajax_loader').css("display","none");
								$("#console").html("<div><b>Stav:</b> prebieha simulácia...</div>");
								if (typeof(jresp[j].sur.out1) != "undefined"){
									d1.push([jresp[j].sur.time,jresp[j].sur.out1]);
									d2.push([jresp[j].sur.time,jresp[j].sur.out2]);
									d3.push([jresp[j].sur.time,jresp[j].sur.out3]);
									d4.push([jresp[j].sur.time,jresp[j].sur.out4]);
									d5.push([jresp[j].sur.time,jresp[j].sur.out5]);
									d6.push([jresp[j].sur.time,jresp[j].sur.out6]);
									d7.push([jresp[j].sur.time,jresp[j].sur.inp]);
									indata=indata+("out1 = "+jresp[j].sur.out4+", time = "+jresp[j].sur.time+"<br />");
									
									
									//var point = [jresp[j].sur.time,jresp[j].sur.out3];
									//var input = [jresp[j].sur.time,jresp[j].sur.inp];
									//var temperature = [jresp[j].sur.time,jresp[j].sur.out1];
									
									
									seriesObject.point = [jresp[j].sur.time,jresp[j].sur.out3];
									seriesObject.input = [jresp[j].sur.time,jresp[j].sur.inp];
									//seriesObject.temperature = [jresp[j].sur.time,jresp[j].sur.out1];
									
									//alert(seriesObject.point);
									
									maximum = Math.max(jresp[j].sur.out4, jresp[j].sur.inp, maximum);
								}else{
									if	 (jresp[j].sur.contains("Warning")){
										msg = msg + "<div class='warning'>"+jresp[j].sur+"</div>";
										//alert('Warning: ' + msg);
									}else if (jresp[j].sur.contains("Simulation problem")){
										msg = msg + "<div class='sim_err_msg'>"+jresp[j].sur+"</div>";
										msg_prefix = "&nbsp;&nbsp;&nbsp;&nbsp;";
										//alert('Problem: ' + msg);
										//hide_window();
									}else if (jresp[j].sur.contains("error")){
										msg = msg + "<div class='sim_err_msg'>"+jresp[j].sur+"</div>";
										msg_prefix = "&nbsp;&nbsp;&nbsp;&nbsp;";
										error_accoured = true;
										//alert('Error: ' + msg);
										//hide_window();
									}else{
										msg = msg + msg_prefix+jresp[j].sur+"<br />";
										//alert(msg);
									}
									//finish=true;
								}
							}
						}
						$("#console2").html(msg);
						finish = ((error_accoured || finish) ? true : false);
						msg = "";
						msg_prefix = "";
						
						/**/
						$('#chart_ajax_loader').hide();
						if(!error_accoured){
							plot_refresh(seriesObject);	//vykresli alebo updatne graf
						}
						/**/
						
						indata="";
					}else{
						$("#console").append(resp);
						resp=" ";
						finish=true;
						$('.ajax_loader').css("display","inline");
						$("#meta").html("<b>Stav:</b> Setting default values...");
					}
				}
		});	
		setTimeout("read_req()",f.sample_time.value);
	}else{
		if(error_accoured){
			$('#placeholder').css("display","none");
			$('.ajax_loader').css("display","none");
		}
		clearTimeout();
	}
}

function plot_refresh(object){
	if(typeof(jresp[1]) == "undefined")return;
	
	if(render_graf == 1){
		//chart - handler grafu - superglobalna premenna
		chart = new Highcharts.Chart({
			chart: {
				renderTo: 'chart_container',
				defaultSeriesType: 'spline',
				events: {
					/*load: requestData,*/
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
			series: [{
						name: 'Sveteľná intenzita',
						color: '#EDC240',
						data: [],
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
						name: 'Vstup',
						color: '#AFD8F8',
						data: [],
						marker: {
							symbol: 'circle',
							enabled: false,
							states: {
								hover: {
									enabled: true
								}
							}
						}
					}/*,
					{
						name: 'Teplota',
						color: '#EDC240',
						data: [],
						marker: {
							symbol: 'circle',
							enabled: false,
							states: {
								hover: {
									enabled: true
								}
							}
						}
					}*/
					
					]
		});
		render_graf = 0;
	}else{
		//tu budem pridavat body
		var series = chart.series[0];
		var inputSeries = chart.series[1];
		//var temperatureSeries = chart.series[2];
		
		//inputSeries.hide();
		
		
		if(typeof object.point !== 'undefined'){
			tempPointsArray.push(object.point);
			tempInputPointsArray.push(object.input);
			//tempTemperatureArray.push(object.temperature);
			
			if(isPauseForChartDrawing){
				chart.series[0].addPoint(object.point, true, false);
				chart.series[1].addPoint(object.input, true, false);
				//chart.series[2].addPoint(object.temperature, true, false);
			}
		}
	}
	
}

function exp_init(){
	//moje init
	tempPointsArray = []; // vynulujem pole s datami pre graf
	tempInputPointsArray = [];
	$('#chart_container').html('');
	$('#console2').html('');
	render_graf = 1;
	
	
	//jeho init
	var f = document.formular;
	old_input = 0;
	zmen_vstup(f, false);
	old_input = f.vstup.value;
	d1= []; d2= []; d3= []; d4= []; d5= []; d6= []; d7= [];
	jresp="";
	msg="";
	msg_prefix = "";
	error_accoured = false;
	started=0;
	finish=0;
	step=f.sample_time.value;
	ajax_set_sess();	//nastavime session_id na gate
}


function zmen_vstup(f,as){
	$.ajax({
		type: "POST",
		url: ROOT_PATH + "includes/modules/experiment/experiment_change_input.php",
		data:	"zmena="+(f.vstup.value-old_input)+
			"&plant_id=0"+
			"&gate_session="+gate_session,
		async: false,
		success: 
			function(html){	
				$("#console").append(" Zmena: "+html+"<br />");
		}
	});
}

function ajax_set_sess(){
	$.ajax({
		type:	"GET",
		url:	ROOT_PATH + "includes/modules/experiment/experiment_get_session.php",
		data:	"plant_id=0",
		cache: false,
		async:	false,
		success: 
			function(html){
				gate_session = html;
			}
	});
}

function IsJsonString(str) {
	try {
		JSON.parse(str);
	} catch (e) {
		return false;
	}
	return true;
}


