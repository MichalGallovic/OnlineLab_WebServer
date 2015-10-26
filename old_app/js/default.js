$(document).ready(function(){  
	
	$('a').blur();
	
	if($(window).width() < 800 || screen.width < 800){
		$('#livechart-modul').attr('style', '');
		$('#reservation-modul').attr('style', '');
		$('#experiment-modul').attr('style', '');
		//$("#containment-wrapper").css('height','800px');
	}else{
		var containmentWrapperHeight =  $(document).height() - $("#dashboard_header").outerHeight() - 20;
    	if($('#dashboard').length != 0)
			$("#containment-wrapper").css('height',containmentWrapperHeight);		 
	}

	
	$("a.collapse").click(function(){
	  $(this).hide();
	  $('.uncollapse').css('display','block');
	  $('#dashboard_left-navig').css('width','50');
	  $('#left_navig_back').css('width','50');
	  $('#dashboard_mainview').css('margin-left','51px');
	  $('ul.menu').hide();
	  $('.left_navig_title').hide();
	  $('.collpase-menu').show();
	  $('.menu').addClass('nodisplay');
	  $('.left_navig_title').addClass('nodisplay');
	  $('.uncollapse').removeClass('nodisplay');
	  $('.collpase-menu').removeClass('nodisplay');
	  var postString = 'action=cahngeMenu&value=2';
	  $.post(ROOT_PATH + "ajax.php", postString, function(theResponse){});
	});
	
	$("a.uncollapse").click(function(){
	  $(this).hide();
	  
	  $('.collapse').css('display','block');
	  $('#dashboard_left-navig').css('width','212');
	  $('#left_navig_back').css('width','212');
	  $('#dashboard_mainview').css('margin-left','213px');
	  $('ul.menu').show();
	  $('.left_navig_title').show();
	  $('.collpase-menu').hide();
	  $('.menu').removeClass('nodisplay');
	  $('.left_navig_title').removeClass('nodisplay');
	  $('.collapse').removeClass('nodisplay');
	  
	   var postString = 'action=cahngeMenu&value=1';
	  $.post(ROOT_PATH + "ajax.php", postString, function(theResponse){});
	});
	

	
	
	//registracia
	$("#register-form-holder").dialog({
			autoOpen: false,
			width: 336,
			modal: true,
			position: ['center', 'center'],
			draggable: false,
			dialogClass: 'login-dialog'
			//buttons: {}
			
		});
	
	$(".register-btn").click(function(){
	 	$("#register-form-holder").dialog("open"); 
	});
	
	$(".close-register-btn").click(function(){
		$("#register-form-holder").dialog("close"); 
	});
	
	//pracovna plocha -nastavenia - plavajuce okna
	$( "#dashboard-settings .default-box" ).draggable({ 
		containment: "#containment-wrapper",
		stack: "#dashboard-settings .default-box",
		opacity:0.7,
		stop: function(event, ui) { 
					var position = $(this).position();
					var currentId = $(this).attr('id');
					/*nazbiereanie vsetkych z indexov*/
					var all_zindex = new Array();
					var zindexObject = new Object; 
					$("#dashboard-settings .default-box").each(function() {
						var modul = $(this).attr('id');
						zindexObject[modul] = parseInt($(this).css("zIndex"), 10);
					});
					
					//alert(JSON.stringify(zindexObject));
					
					var postString = 'left=' + position.left + '&element=' + currentId + '&top=' + position.top + '&action=updateChartPosition';
					$.post(ROOT_PATH + "ajax.php", postString, function(theResponse){});
					var postString = 'zindex=' + JSON.stringify(zindexObject) + '&action=updateZindex';
					$.post(ROOT_PATH + "ajax.php", postString, function(theResponse){});
				}
	});
	
	//pracovna plocha - plavajuce okna
	$( "#dashboard .default-box" ).draggable({ 
		containment: "#containment-wrapper",
		stack: "#dashboard .default-box",
		opacity:0.7,
		handle: "div.header",
		stop: function(event, ui) { 
					var position = $(this).position();
					var currentId = $(this).attr('id');
					/*nazbiereanie vsetkych z indexov*/
					var all_zindex = new Array();
					var zindexObject = new Object; 
					$("#dashboard .default-box").each(function() {
						var modul = $(this).attr('id');
						zindexObject[modul] = parseInt($(this).css("zIndex"), 10);
					});
					
					
					
 					var postString = 'left=' + position.left + '&element=' + currentId + '&top=' + position.top + '&action=updateChartPosition';
					$.post(ROOT_PATH + "ajax.php", postString, function(theResponse){});
					var postString = 'zindex=' + JSON.stringify(zindexObject) + '&action=updateZindex';
					$.post(ROOT_PATH + "ajax.php", postString, function(theResponse){});
				}
	});
	
	//pracovna ploch resizable
	$( "#dashboard-settings .default-box:not(#livechart-modul)" ).resizable({
		stop:function(event, ui){
			var currentId = $(this).attr('id');
			var width = ui.size.width;
			var height = ui.size.height;
			var postString = 'width=' + width + '&element=' + currentId + '&height=' + height + '&action=updateChartDimenstions';
			$.post(ROOT_PATH + "ajax.php", postString, function(theResponse){});
		}
	});
	
	$("#dashboard-settings #reservation-modul").resizable({minHeight: 122,minWidth: 400});
	$("#dashboard-settings #experiment-modul").resizable({minHeight: 125,minWidth: 394});
	$("#dashboard-settings #experimentinterface-modul").resizable({minHeight: 65,minWidth: 400});
	
	
	//pracovan plocha options panel
	// Expand Panel
	$("#open-options").click(function(){
		$("#dashboard-options-wrapper").slideDown("slow");
		$("#dashboard-btn-info-wrapper").hide();
	});	
	
	// Collapse Panel
	$("#close-options").click(function(){
		$("#dashboard-options-wrapper").slideUp(function(){
			$("#dashboard-btn-info-wrapper").show();
		});
		
	});	
	
	$("#info-dashboard-open").click(function(){
		$("#dashboard-info-wrapper").slideDown("slow");
		$("#dashboard-btn-options-wrapper").hide();
		$("#dashboard-btn-info-wrapper").css('margin-right','186px');
	});
	$("#info-dashboard-close").click(function(){
		$("#dashboard-info-wrapper").slideUp(function(){
			$("#dashboard-btn-info-wrapper").css('margin-right','10px');
			$("#dashboard-btn-options-wrapper").show();											  
		});
	});
	
	$('#dashboard-btn-options-wrapper a').click(function () {
		$("#dashboard-btn-options-wrapper a").toggle();
	});
	
	$('#dashboard-btn-info-wrapper a').click(function () {
		$("#dashboard-btn-info-wrapper a").toggle();
	});
	
	//button pre dafaultne nastavanie pracovnej plochy
	$('#btn-default-settings').click(function(){
		$("#dashboard-options-wrapper").slideUp("slow");
		$("#dashboard-info-wrapper").slideUp("slow");
		$('.dashboard_overlay').show();
		$('#dashboard-btn-info-wrapper').show();
		
		var profileValue = $('input:radio[name=profile]:checked').val();
		var postString = 'action=setDefayltDashboardSettings&profile=' + profileValue;
		$.post(ROOT_PATH + "ajax.php", postString, function(data){
			
			for(i = 0; i< data.modules.length; i++){
				if(data.modules[i].show > 0){
					$("#" + data.modules[i].modul + "-modul").show();
					$('#' + data.modules[i].modul + '-modul_' + profileValue).attr('checked',true);
				}else{
					$("#" + data.modules[i].modul + "-modul").hide();	
					$('#' + data.modules[i].modul + '-modul_' + profileValue).attr('checked',false);
				}
				
				var height = data.modules[i].height;
				if(height < 1){
					$("#" + data.modules[i].modul + "-modul").animate({
						left: data.modules[i].left_,
						top:  data.modules[i].top,
						width:data.modules[i].width
					},750);
					
					
					$("#" + data.modules[i].modul + "-modul").css('height','auto');
					$("#" + data.modules[i].modul + "-modul").css('min-height','');
					
				}else{
					$("#" + data.modules[i].modul + "-modul").animate({
						left: data.modules[i].left_,
						top:  data.modules[i].top,
						width:data.modules[i].width,
						height:height
					},750);	
					
					if(data.modules[i].modul === 'livechart'){
						
						$('#chart_container').css('height','240px');	
						$('#chart_container').css('width','800px');	
					}
				}
				
			}
			
			$.post(ROOT_PATH + "includes/modules/livechart/ajax.php",{action: "getLastReport"} ,function(data) {
				$('#last-measurment-title').remove();
				$("#chart_container").html(data.html);
					if(data.chartData.length <= 0)
						$("#intro").html(data.chartIntro);
					else{
						renderLasrReportChart(data.chartData);
						$('#livechart-modul .header ').append('<span id="last-measurment-title">'+data.title+'</span>');
					}
				  },"json");
			
			$('.dashboard_overlay').hide();
		},'json');
	});
	
	//vyber profilu
	$("input[name=profile]").click(function(){
		$('.dashboard_overlay').show();
		var selectedProfile = $(this).val();
		
		
		
		var modulesProfileId = 'modules-profile-'+ selectedProfile;
		$("#modules-profile-1").hide();
		$("#modules-profile-2").hide();
		$("#modules-profile-3").hide();
		$("#"+modulesProfileId).show();
		$("#"+modulesProfileId).removeClass('nodisplay');
		
		var postString = 'action=selectProfile&profile=' + selectedProfile;
		$.post(ROOT_PATH + "ajax.php", postString, function(data){

			for(i = 0; i< data.modules.length; i++){
				
				if(data.modules[i].show > 0){
					$("#" + data.modules[i].modul + "-modul").show();
					$("#" + data.modules[i].modul + "-modul").removeClass('nodisplay');
				}else{
					$("#" + data.modules[i].modul + "-modul").hide();
				}
				
				var height = data.modules[i].height;
				if(height < 1){
					$("#" + data.modules[i].modul + "-modul").animate({
						left: data.modules[i].left_,
						top:  data.modules[i].top,
						width:data.modules[i].width
					},750);
					$("#" + data.modules[i].modul + "-modul").css('height','auto');
					$("#" + data.modules[i].modul + "-modul").css('min-height','');
				}else{
					$("#" + data.modules[i].modul + "-modul").animate({
						left: data.modules[i].left_,
						top:  data.modules[i].top,
						width:data.modules[i].width,
						height:height
					},750);	
	
				}
				
				
			}
			
			$('.dashboard_overlay').hide();
		},'json');
	});
	
	
	//info okno
	var $infoDialogContent = $("#napoveda");
	$infoDialogContent.dialog({
		width: 400,
		dialogClass: "info-dialog",
		modal: true,
		position: ['center','center']	
		
	});
	$('#btn-dashboard-layout-info-text').click(function(){ 
	    if($('#show-dashboard-layout-info-text').is(':checked')){
			var postString = 'action=hideDashboardSettingsInfo';
			$.post(ROOT_PATH + "ajax.php", postString, function(theResponse){});	
		}
		$infoDialogContent.dialog("close"); 
	});
	//end info okno
	
	
	
	//checkboxy pre  zobrazenie okine na peacovnej ploche
	$("#dashboard-options-wrapper input[type='checkbox']").click(function(){
		var checboxName = $(this).attr('name');
		var nameAndId  = checboxName.split("_");
		var boxIdName = nameAndId[0].toString();
		
		
		if($(this).is(':checked')){
			$('#' + boxIdName).show();
			$('#' + boxIdName).removeClass('nodisplay');
			$.post(ROOT_PATH + "ajax.php",{"action":"setDashboardBox","box":boxIdName}, function(data) {});
		}else{
			$('#' + boxIdName).hide();
			$.post(ROOT_PATH + "ajax.php",{"action":"unsetDashboardBox","box":boxIdName}, function(data) {});
		}
		
	});
	
}); 

