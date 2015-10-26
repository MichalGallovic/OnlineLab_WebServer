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
var msg2 = "";
var msg_prefix="";
var gate_session="";
var sess_backup="";
var f;
var plotdata = new Array();
var d1 = []; var d2= []; var d3= []; var d4= []; var d5= []; var d6= []; var d7= [];

var equipment;
var render_graf = 1; // premenna ktora mi urci ci do grafu pridat bod alebo ho iba nacitat.
var seriesObject = new Object; 
var fullSeriesObject = new Object; // objekt pre reporty
var exp_from_termo_interface = false; //premenna indikuje z kade pustame exp. (dialogove okno / interface)

/*var intenzitaSeries = []
var inputSeries = [];
var temperatureSeries = [];
var ftemperatureSeries = [];
var fintenzitaSeries = [];
var currentSeries = [];*/

function empty() {this.splice(0,this.length);}	//pridame metodu vyprazdnenia pre triedu Array
Array.prototype.empty=empty;
function contains(it) { return this.indexOf(it) != -1; }
String.prototype.contains = contains;

$(document).ready(function(){  
	var noAvaibleWindow = $("#no-avaible-window");
	
	$('#ctrl_set').change(function(){update_ctrl_body();});
	
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
	
	//$(window).bind('beforeunload', function(){return "Ste si istý, že chcete opustiť stránku?\n Ak máte spustený experiment, tak bude ukončený.";});
	$(window).unload(function(){
		
		var s =  new Object;
		s.intenzita = d3;
		s.f_intenzita = d4;
		s.input = d7;
		s.temperature = d1;
		s.f_temperature = d2;
		s.current = d5;
		s.rotaion = d6;
		
		if(!finish){					
			finish = true;
			$('#stop-chart').show();
			$('#stop-continue-btn span#stop-measurement').show();
			
			var LastPointString =  d3[d3.length-1].toString();
			var LastPoint = LastPointString.split(',');
			//alert(LastPoint[0].toString());
			//alert(JSON.stringify(s));
			
			//var postString = "saveReport=1&output="+JSON.stringify(series)+"&consoleOutput="+msg+"&report_simulation_time="+reportLastPoint[0];
			$.post(ROOT_PATH + "includes/modules/experiment/ajax_save_experimet.php",{"saveReport":1,"output":JSON.stringify(s),"consoleOutput":msg2,"report_simulation_time" : LastPoint[0].toString()},function(data){});
								
			
			$.ajax({
				type:	"POST",
				url:	ROOT_PATH + "includes/modules/experiment/ajax_experiment_gate_stop.php",
				data:	"terminate=1&plant_id="+f.plant_id.value+"&gate_session="+gate_session,
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
	
	//simulacia termo zariadenia
	$("#termo").click(function(){
		  var f = document.formular;
		  simuluj(f);
	});
	
	//simulacia termo z interface
	$('#termo-from-interface').click(function(){
		exp_from_termo_interface = true	;
		var f = document.formular2;
		simuluj(f);
	});
	
});

function simuluj(f){
	start_req(f);
	//a nasledne spustime funkciu na rtm citanie udajov
	read_req();
	
}

function set_interface(form){
	
	
	//ak je na ploche zobrazeny
	if ($('#experimentinterface-modul').is(':visible') ){
		
		//pozadovana hodnota
		$('#experimentinterface-termo input[name="vstup"]').val(form.vstup.value);
		//cas simulacie
		$('#experimentinterface-termo input[name="time"]').val(form.time.value);
		//perioda vzorkovania
		$('#experimentinterface-termo input[name="sample_time"]').val(form.sample_time.value);
		//reulovana velicina
		$('#experimentinterface-termo select[name="vyst_switch"]').val(form.vyst_switch.value);
		//regulacna velicina
		$('#experimentinterface-termo #vstup_switch-intreface').val(form.vstup_switch.value);
		
		
		$('#c_vst_1-intreface, #c_vst_2-intreface, #c_vst_3-intreface').hide();
		//napatie lampy
		if( $('#c_vst_1').css('display') !== 'none' ){$('#c_vst_1-intreface').show();}
		$('#experimentinterface-termo input[name="c_lamp"]').val(form.c_lamp.value);
		
		//Napätie LED diódy
		if( $('#c_vst_2').css('display') !== 'none' ){$('#c_vst_2-intreface').show();}
		$('#experimentinterface-termo input[name="c_led"]').val(form.c_led.value);
		//Napätie motorčeka 
		if($('#c_vst_3').css('display') !== 'none'){$('#c_vst_3-intreface').show();}
		$('#experimentinterface-termo input[name="c_fan"]').val(form.c_fan.value);
		
		//aky typ reg sme si zvolili 
		var controlType =  getCheckedValue(form.ctrl_typ);
		$('#experimentinterface-termo .pid_reg,#experimentinterface-termo .own_reg,#experimentinterface-termo .no_reg').hide();
		//pid
		$('#experimentinterface-termo input[name="P"]').val(form.P.value);
		$('#experimentinterface-termo input[name="I"]').val(form.I.value);
		$('#experimentinterface-termo input[name="D"]').val(form.D.value);
		if( controlType === 'PID'){
			$('#experimentinterface-termo .pid_reg').show();
			$('#experimentinterface-termo :radio[value=PID]').attr('checked',true);
		}
		$('#experimentinterface-termo textarea[name="own_func"]').val(form.own_func.value);
		if( controlType === 'OWN'){
			$('#experimentinterface-termo .own_reg').show();
			$('#experimentinterface-termo :radio[value=OWN]').attr('checked',true);
		}
		if( controlType === 'NO'){
			$('#experimentinterface-termo .no_reg').show();
			$('#experimentinterface-termo :radio[value=NO]').attr('checked',true);
		}
		
		$("#experimentinterface-intro").hide();
		$("#experimentinterface-termo").show();
	}		
}

function start_req(f){
	
	//vypis 
	show_listener("Simulácia sa začína...");
	//loadovanie- zaciatok
	$('#chart_ajax_loader').show();
	//zavrem okno
	$("#" + equipment).dialog("close");
	
	//nastavim pociatocne podmienky
	exp_init();
	//nastavim rozhranie
	set_interface(f);
	
	$('#button_stop_sim').css('display','block');
	$('#stop-continue-btn').css('display','block');
	$('#last-measurment-title').hide();
	
	if(exp_from_termo_interface)
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
					 "&ctrl_typ="+getCheckedValue(f.ctrl_typ)+
					 "&OWN="+(f.ctrl_typ[2].checked ? "2" : (f.ctrl_typ[1].checked ? "1" : "0"))+
					 "&scifun="+encodeURIComponent(f.own_func.value)+
					 //v nasledovnom riadku otestujeme ci bola vykonana nejaka zmena v tele regulatora alebo nie. Ak nie, tak ulozime id.
					 "&own_ctrl_id="+($('#own_func').val() == ajax_get_ctrl($('#ctrl_set').val()) ? $('#ctrl_set').val() : -1)+
					 "&plant_id="+f.plant_id.value+
					 "&gate_session="+gate_session;
	else
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
						 "&ctrl_typ="+getCheckedValue(f.ctrl_typ)+
						 "&OWN="+(f.ctrl_typ[2].checked ? "2" : (f.ctrl_typ[1].checked ? "1" : "0"))+
						 "&scifun="+encodeURIComponent(f.own_func.value)+
						 //v nasledovnom riadku otestujeme ci bola vykonana nejaka zmena v tele regulatora alebo nie. Ak nie, tak ulozime id.
						 "&own_ctrl_id="+($('#own_func-interface').val() == ajax_get_ctrl($('#ctrl_set-interface').val()) ? $('#ctrl_set-interface').val() : -1)+
						 "&plant_id="+f.plant_id.value+
						 "&gate_session="+gate_session;
	
	$.post(ROOT_PATH + "includes/modules/experiment/ajax_experiment_gate_model_runner.php",postString,
		   function(data){
		   		if(data.error == 1){
					error_accoured = true;
					$("#console").html('');
					alert(data.msg);
					$('#chart_ajax_loader').hide();
					$("#action_listener").hide();
				}
				
				if(data.error == 0){
					//$('#console').append("<b>Stav:</b> "+data.output);
					show_listener(data.output);
					setTimeout('$("#action_listener").hide()',1500);
				}
		   
		   },"json"
	);
}


function read_req(){
	if(exp_from_termo_interface)
		var f = document.formular2;	
	else
		var f = document.formular;
	if(!finish){
		$.ajax({
			type: "POST",
			url: ROOT_PATH + "includes/modules/experiment/ajax_experiment_gate_rtm_read.php",
			dataType: "text",
			cache: false,
			data: "plant_id="+f.plant_id.value+"&gate_session="+gate_session,
			async: false,
			success: 
				function(resp){//alert(resp);
                                        
					if(!started && !error_accoured){started=1;}
					if(IsJsonString(resp)){
						d1= []; d2= []; d3= []; d4= []; d5= []; d6= []; d7= [];
						seriesObject = {};
						jresp=JSON.parse(resp);
						
						for(j=0; j<jresp.length; j++ ){
							if(jresp[j].done=="true"){
								msg = msg + jresp[j].sur+"<br />";finish=true;
								$('#button_stop_sim').hide();
								$('#stop-continue-btn').hide();
								
								show_listener('Koniec experimentu...');
								setTimeout('$("#action_listener").hide()',1500);
								
								fullSeriesObject.intenzita = d3;
								fullSeriesObject.f_intenzita = d4;
								fullSeriesObject.input = d7;
								fullSeriesObject.temperature = d1;
								fullSeriesObject.f_temperature = d2;
								fullSeriesObject.current = d5;
								fullSeriesObject.rotaion = d6;
								
								
								var reportLastPointString =  d3[d3.length-1].toString();
								var reportLastPoint = reportLastPointString.split(',');
								
								var postString = "saveReport=1&output="+JSON.stringify(fullSeriesObject)+"&consoleOutput="+msg+"&report_simulation_time="+reportLastPoint[0];
								
								$.post(ROOT_PATH + "includes/modules/experiment/ajax_save_experimet.php",{"saveReport":1,"output":JSON.stringify(fullSeriesObject),"consoleOutput":msg,"report_simulation_time" : reportLastPoint[0].toString()},function(data){});
								
								//alert(fullSeriesObject.intenzita);
								//console.log(JSON.stringify(fullSeriesObject));
							}else{
								show_listener('Prebieha simulácia...');
								//$("#console").html("<div><b>Stav:</b> prebieha simulácia...</div>");
								if (typeof(jresp[j].sur.out1) != "undefined"){
									d1.push([jresp[j].sur.time,jresp[j].sur.out1]);
									d2.push([jresp[j].sur.time,jresp[j].sur.out2]);
									d3.push([jresp[j].sur.time,jresp[j].sur.out3]);
									d4.push([jresp[j].sur.time,jresp[j].sur.out4]);
									d5.push([jresp[j].sur.time,jresp[j].sur.out5]);
									d6.push([jresp[j].sur.time,jresp[j].sur.out6]);
									d7.push([jresp[j].sur.time,jresp[j].sur.inp]);
									
									seriesObject.point = [jresp[j].sur.time,jresp[j].sur.out3];
									seriesObject.f_intenzita = [jresp[j].sur.time,jresp[j].sur.out4];
									seriesObject.input = [jresp[j].sur.time,jresp[j].sur.inp];
									seriesObject.temperature = [jresp[j].sur.time,jresp[j].sur.out1];
									seriesObject.f_temperature = [jresp[j].sur.time,jresp[j].sur.out2];
									seriesObject.current = [jresp[j].sur.time,jresp[j].sur.out5];
									seriesObject.rotaion = [jresp[j].sur.time,jresp[j].sur.out6];
									
									indata=indata+("out1 = "+jresp[j].sur.out4+", time = "+jresp[j].sur.time+"<br />");
									
									$('#button_hide_graf').css("display","inline");
									
									maximum = Math.max(jresp[j].sur.out4, jresp[j].sur.inp, maximum);
								}else{
                                                                    
									if	 (jresp[j].sur.contains("Warning")){
										msg = msg + "<div class='warning'>"+jresp[j].sur+"</div>";
									}else if (jresp[j].sur.contains("Simulation problem")){
										msg = msg + "<div class='sim_err_msg'>"+jresp[j].sur+"</div>";
										msg_prefix = "&nbsp;&nbsp;&nbsp;&nbsp;";
										//hide_window();
									}else if (jresp[j].sur.contains("error")){                                                                                
										msg = msg + "<div class='sim_err_msg'>"+jresp[j].sur+"</div>";
										msg_prefix = "&nbsp;&nbsp;&nbsp;&nbsp;";
									
										alert('Chyba: nedovolené nastavenie regulátora.');

										$("#own_func").val('y1=u1');
										$.post(ROOT_PATH + "includes/modules/experiment/ajax_delete_last_report.php");
										error_accoured = true;
										$('#chart_ajax_loader').hide();
										$('#chart_container').html('');
										//window.location.reload();
										
										//hide_window();
									}else{
										msg = msg + msg_prefix+jresp[j].sur+"<br />";
									}
									//finish=true;
								}
							}
						}
						//$("#console2").html(msg);
						finish = ((error_accoured || finish) ? true : false);
						msg2 = msg+'simulate end(by page leaving)';
						msg = "";
						msg_prefix = "";
						$("#placeholder").html("");
						
						
						plot_refresh(seriesObject);	//vykresli alebo updatne graf

						indata="";
					}else{
						//$("#console").append(resp);
						//show_listener(resp);	
						resp=" ";
						finish=true;
										
						//$("#console").html("<b>Stav:</b> Setting default values...");
						
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





function zmen_vstup(f,as){
	$.ajax({
		type: "POST",
		url: ROOT_PATH + "includes/modules/experiment/ajax_experiment_gate_change_input.php",
		data:	"zmena="+(document.formular.vstup.value-old_input)+
			"&plant_id="+f.plant_id.value+"&gate_session="+gate_session,
		async: as,
		success: 
			function(html){	
				$("#meta").append(" Zmena: "+html);
		}
	});
}
function ajax_get_ctrl(ctrl_id){
	var ctrl_body = "";
	$.ajax({
		type:	"POST",
		url:	ROOT_PATH +"includes/modules/experiment/ajax_experiment_get_ctrl.php",
		data:	"ctrl_id="+ctrl_id,
		async:	false,
		success: 
			function(html){
				ctrl_body = html;
			}
	});
	return ctrl_body;
}
function ajax_set_sess(){
	var f = document.formular;
	$.ajax({
		type:	"GET",
		url:	ROOT_PATH + "includes/modules/experiment/ajax_experiment_gate_set_session.php",
		data:	"plant_id="+f.plant_id.value,
		cache: false,
		async:	false,
		success: 
			function(html){
				gate_session = html;
			}
	});
}

function update_ctrl_body(){
	var own_ctrl_id = $('#ctrl_set').val();
	var ctrl_body = ajax_get_ctrl(own_ctrl_id);
	if(ctrl_body != "permission denied"){
		$('#own_func').val(ctrl_body);
	}else{
		$('#own_func').val("Nemáte oprávnenie zobraziť regulátor s id: "+own_ctrl_id);
	}
}

function exp_init(){
	//moje init
	tempPointsArray = []; // vynulujem pole s datami pre graf
	tempInputPointsArray = [];
	tempTemperatureArray = [];
	tempFilterTemperatureArray = [];
	tempFilterIntenzitaArray = [];
	tempCurrentArray = [];
	tempRotationArray = [];
	$('#chart_container').html('');
	$('#console2').html('');
	$('#intro').hide();
	$("#experimentinterface-intro").show();
	$("#experimentinterface-termo").hide();
	render_graf = 1;
	
	var f = document.formular;
	old_input = 0;
	zmen_vstup(f, false);	// inicializujeme shm pre povodnu hodnotu vstupu na 0
	old_input = f.vstup.value;
	d1= []; d2= []; d3= []; d4= []; d5= []; d6= []; d7= [];
	jresp="";
	msg="";
	msg_prefix = "";
	error_accoured = false;
	started=0;
	finish=0;
	fullSeriesObject = new Object;
	$('#err_msg').html("");
	step=f.sample_time.value;
	plotdata.empty();
	$("#console").html("");
	$("#placeholder").html("");
	
	
	//sess_backup = ajax_sess_backup(0);alert("saveing session: "+sess_backup);
	ajax_set_sess();	//nastavime session_id na gate
}


function plot_refresh(object){
	if(typeof(jresp[1]) == "undefined")return;
	
	if(render_graf == 1){
		$.post(ROOT_PATH + "includes/modules/experiment/ajax_save_experimet.php",{"startReport":1},function(data){});
								
		//chart - handler grafu - superglobalna premenna
		//chart.destroy();
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
						color: '#7a5c01',
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
						color: '#0516f7',
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
						name: 'Teplota',
						color: '#fa0000',
						data: [],
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
						name: 'Filtrovaná teplota',
						color: '#fdb1b1',
						data: [],
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
						name: 'Filtrovaná sveteľná intenzita',
						color: '#EDC240',
						visible: false,
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
						name: 'Prúd',
						color: '#fff600',
						data: [],
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
						name: 'Otáčky ventilátoraRPM',
						color: '#cc00ff',
						data: [],
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
					}
					
					]
		});
		render_graf = 0;
	}else{
		var intenzitaSeries = chart.series[0];
		var inputSeries = chart.series[1];
		var temperatureSeries = chart.series[2];
		var ftemperatureSeries = chart.series[3];
		var fintenzitaSeries = chart.series[4];
		var currentSeries = chart.series[5];
		var retaionSeries = chart.series[6];
		
		//teplota na yaictku schovame
		//temperatureSeries.hide();
		//ftemperatureSeries.hide();
		//fintenzitaSeries.hide();
		//currentSeries.hide();
		//retaionSeries.hide();
		
		if(typeof object.point !== 'undefined'){
			//zbieranie vsetkych bodov do temporaraneho pola..
			tempPointsArray.push(object.point);
			tempInputPointsArray.push(object.input);
			tempTemperatureArray.push(object.temperature);
			tempFilterTemperatureArray.push(object.f_temperature);
			tempFilterIntenzitaArray.push(object.f_intenzita);
			tempCurrentArray.push(object.current);
			tempRotationArray.push(object.rotaion);
			
			if(isPauseForChartDrawing){
				intenzitaSeries.addPoint(object.point, true, false);
				inputSeries.addPoint(object.input, true, false);
				temperatureSeries.addPoint(object.temperature, true, false);
				ftemperatureSeries.addPoint(object.f_temperature, true, false);
				fintenzitaSeries.addPoint(object.f_intenzita, true, false);
				currentSeries.addPoint(object.current, true, false);
				retaionSeries.addPoint(object.rotaion, true, false);
			}
		}
	}
	
}


function add_to_ready_exp(){}

function IsJsonString(str) {
	try {
		JSON.parse(str);
	} catch (e) {
		return false;
	}
	return true;
}

function show_listener(text){
	$("#action_listener").html(text);
	$("#action_listener").css('display','inline-block');
	//setTimeout('$("#action_listener").hide()',1500);
	
}

function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}


