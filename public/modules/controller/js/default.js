$(document).ready(function(){

	//span na lisitng regulatorov z preview
	$('#back-to-ctrl').click(function(){
		$('#reg-preview').slideUp(function(){
			$('#controller_list').slideDown(function(){
				loadTable();
			});	
		});									  
	});
	
	//span na lisitng regulatorov z nastavenia reg.
	$('#back-to-ctrl2').click(function(){
		$('#reg-settings').slideUp(function(){
			$('#controller_list').slideDown(function(){
				loadTable();
			});	
		});									  
	});
	
	$('#back-to-selected-reg').click(function(){
		var regId = $("#settings_ctrl_id").html().toString();
		$("#reg-settings").slideUp(function(){
			show_reg(regId);
		});
		
	});
	
	//vytvorime novy reg
	$('#create-new-ctrl').click(function(){
		
		$("#reg-new-form").dialog({
			width: 356,
			dialogClass: "info-dialog",
			modal: true,
			position: ['center','center']								   
		}); 
		
		$(".info-dialog-close-btn").click(function(){
			$("#reg-new-form").dialog("close");									   	
		});
	});
	
	$('#change-cltr-settings').click(function(){
		var ctrl_id = $('#ctrl_id_input').val();
		
		$("#reg-settings-box").hide();
		$('#reg-preview').slideUp(function(){
			$('#reg-settings').slideDown(function(){
				$.post(ROOT_PATH + "includes/modules/controller/ajax.php",{"ctr_settings":1,"regId":ctrl_id}, function(data){
					$('input[name="ctrl_id"]').val(data.ctrl_id);
					$("#settings_ctrl_id").html(data.ctrl_id);
					$("#settings_reg_name").val(data.reg_name);
					$("#settings_reg_body").val(data.reg_body);
					
					if(data.reg_permissions == 1){
						$("#reg-setting-pulbic-yes").attr('checked',true);	
					}else{
						$("#reg-setting-pulbic-no").attr('checked',true);		
					}
					
					$('#equipment_id').val(data.reg_equipment_id);
					
					$("#reg-settings-box").show();
				},"json");	
			});	
		});		
	});

});

function delete_reg(regId){
	$('#regId').val(regId);
	
	$("#delete-reg").dialog({
		width: 356,
		dialogClass: "info-dialog",
		modal: true,
		position: ['center','center']								   
	});
	
	$(".info-dialog-close-btn").click(function(){
		$("#delete-reg").dialog("close");									   	
	});
	
}

function delete_reg_process(){
	var regId = $('#regId').val();
	$("#delete-reg").dialog("close");	
	
	$('#row-' + regId).fadeOut(function(){
		
		$.post(ROOT_PATH + "includes/modules/controller/ajax.php",{"delete_reg":1,"regId":regId}, function(data){
			
		},"json");
		
		$("table.controllers tbody").load(ROOT_PATH + "includes/modules/controller/loaddata.php?action=get_rows");
				
		$.get(ROOT_PATH + "includes/modules/controller/loaddata.php?action=row_count", function(data) {
			$("#page_count").val(Math.ceil(data / rows_per_page));
			generateRows(1);
		});
	});	
}

function settings_reg(regId){
		
		$("#reg-settings-box").hide();
		$('#controller_list').slideUp(function(){
			$('#reg-settings').slideDown(function(){
				$.post(ROOT_PATH + "includes/modules/controller/ajax.php",{"ctr_settings":1,"regId":regId}, function(data){
					$('input[name="ctrl_id"]').val(data.ctrl_id);
					$("#settings_ctrl_id").html(data.ctrl_id);
					$("#settings_reg_name").val(data.reg_name);
					$("#settings_reg_body").val(data.reg_body);
					
					if(data.reg_permissions == 1){
						$("#reg-setting-pulbic-yes").attr('checked',true);	
					}else{
						$("#reg-setting-pulbic-no").attr('checked',true);		
					}
					
					$('#equipment_id').val(data.reg_equipment_id);
					
					$("#reg-settings-box").show();
				},"json");	
			});	
		});			
}

function show_reg(regId){
	
	$('#ctrl_properties').hide();
	$('#controller_list').slideUp(function(){
		$('#pager_holder').hide();
		$('#reg-preview').slideDown(function(){
			$.post(ROOT_PATH + "includes/modules/controller/ajax.php",{"ctrlPreview":1,"regId":regId}, function(data) {
				
				$('#ctrl_id').html(data.ctrl_id);
				$('#ctrl_id_input').val(data.ctrl_id);
				$('#reg_name').html(data.reg_name);
				$('#reg_author').html(data.reg_author);
				$('#reg_date').html(data.reg_date);
				$('#reg_equipment_name').html(data.reg_equipment_name);
				$('#reg_permissions').html(data.reg_permissions);
				$('#reg_body textarea').val(data.reg_body);
				$('#ctrl_properties').show();
			
			},"json");
		});
		
	});
}

function create_new_reg(){
	var form = $('#new-reg').serialize();
	$('.err_warning').hide();
	$('#new-reg input ').removeClass('red-border');
	$('#new-reg textarea').removeClass('red-border');
	
	$.post(ROOT_PATH + "includes/modules/controller/ajax.php", $('#new-reg').serialize(),function(data){
		
		if(data.status === -1){
			$('.err_warning').html(data.msg);
			for(i = 0; i< data.empty.length; i++){
				$('#new-reg input[name="' + data.empty[i] + '"]').addClass('red-border');
				$('#new-reg textarea[name="' + data.empty[i] + '"]').addClass('red-border');
			}
			
			$('#new-reg input[name="' + data.empty[0] + '"]').focus();
			
			$('.err_warning').show();
		}
		
		
		if(data.status === 1){
			
			
			$(".ok_warning").html(data.msg);
		    $("#reg-new-form").dialog("close");
			
			
			$("table.controllers tbody").html('<tr><td colspan="6" style="padding:25px;"><div id="chart_ajax_loader"></div></td></tr>');
			var t =  setTimeout("loadTable()",2500);
			$(".ok_warning").fadeIn().delay(2500).fadeOut();
			
			
		}
		
	},"json");
																									
	
	return false;
}

function save_reg_settings(){
	$('#reg-settings-form').serialize();
	$(".ajax_loader").show();
	$.post(ROOT_PATH + "includes/modules/controller/ajax.php", $('#reg-settings-form').serialize(),function(data){
		
		$(".ajax_loader").hide();
		$(".ok_warning").html(data.msg);
		$(".ok_warning").fadeIn().delay(2500).fadeOut();
		
	},"json");
}

function loadTable(){
	
	$("table.controllers tbody").load(ROOT_PATH + "includes/modules/controller/loaddata.php?action=get_rows");
				
	$.get(ROOT_PATH + "includes/modules/controller/loaddata.php?action=row_count", function(data) {
		$("#page_count").val(Math.ceil(data / rows_per_page));
		generateRows(1);
	});
	
}





