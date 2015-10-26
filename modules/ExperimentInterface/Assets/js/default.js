//js

$(document).ready(function(){  
	//termo checkboxy
	$("#experimentinterface-termo input[name=ctrl_typ]").change( function(){
																  	
		var reg_typ =  $("#experimentinterface-termo input[name='ctrl_typ']:checked").val().toLowerCase();
		$('#experimentinterface-termo .own_reg,#experimentinterface-termo .no_reg,#experimentinterface-termo .pid_reg').fadeOut().delay(600);
		$('#experimentinterface-termo .' + reg_typ + '_reg').fadeIn();	
	});
	
	
	//termo napatie selectbox
	$("#vstup_switch-intreface").change(function(){
		var c_vst_number = $("#vstup_switch-intreface option:selected").val();	
		
		$('#c_vst_1-intreface, #c_vst_2-intreface, #c_vst_3-intreface').show();
		$('#c_vst_' +  c_vst_number + '-intreface').hide();
	});
	
	//zmena vlastneho regulatora
	$('#ctrl_set-interface').change(function(){update_ctrl_body_interface();});
	
});

function update_ctrl_body_interface(){
	var own_ctrl_id = $('#ctrl_set-interface').val();
	var ctrl_body = ajax_get_ctrl(own_ctrl_id);
	if(ctrl_body != "permission denied"){
		$('#own_func-interface').val(ctrl_body);
	}else{
		$('#own_func-interface').val("Nem·te opr·vnenie zobraziù regul·tor s id: "+own_ctrl_id);
	}
}


/*function start_reg_interface(){
	f=document.formular;
	//vypis 
	show_listener("Simul·cia sa zaËÌna...");
	//loadovanie- zaciatok
	$('#chart_ajax_loader').show();
	//zavrem okno
	$("#" + equipment).dialog("close");
	
	//nastavim pociatocne podmienky
	exp_init();
	//nastavim rozhranie
	set_interface(f);		
}*/