//js file

function set_profile(){
	
	var form  = $('#update-prfile-form');
	$("#profile-process-loader").show();
	$.post(ROOT_PATH + "includes/modules/profile/ajax.php",form.serialize(),
		function(data){
			$('#update-prfile-form input').removeClass('red-border');
			
			//neboli vyplnene vsetky polia 
			//yadane heslo bey potvrdenie
			//zadane potvrdenie hesla bez prvotneho zadanioa hesla
			//zadane hesla sa nezhodujue
			//nie je unikatny mail
			if(data.status == -1 || data.status == -2 || data.status == -3 || data.status == -4 || data.status == -5){
				for(i = 0; i< data.empty.length; i++){
					$('#update-prfile-form input[name="' + data.empty[i] + '"]').addClass('red-border');
				}
				$("#empty-fields-message-holder").show();
				$('#update-prfile-form input[name="' + data.empty[0] + '"]').focus();
				$('#profile-warning').html(data.msg);
				$('#profile-warning').fadeIn().delay(2500).fadeOut();
			}
			
			//vsetko ok
			if(data.status == 1){
				$('#profile-succes').html(data.msg);
				$('#profile-succes').fadeIn().delay(2500).fadeOut();			
			}
			
			$("#profile-process-loader").hide();
		},
	"json");
	return false;
}
