function register(){

	$(".reg-loader").show();
	$.post(ROOT_PATH + "includes/modules/registration/ajax.php", $('#registration-form').serialize(),
		function(data){
			/*default settings*/
			$("#registration-form p.info").hide();
			$('#registration-form input').removeClass('red-border');
			$(".reg-loader").hide();
			
			//neboli vyplnene vsetky polia
			if(data.status == -1){
				for(i = 0; i< data.empty.length; i++){
					$('#registration-form input[name="' + data.empty[i] + '"]').addClass('red-border');
				}
				$("#empty-fields-message-holder").show();
				$('#registration-form input[name="' + data.empty[0] + '"]').focus();
			}
			
			//zadane hesla sa nezhoduju
			if(data.status == -2){
				$('#registration-form input[name="pass"]').addClass('red-border');
				$('#registration-form input[name="pass2"]').addClass('red-border');
				$("#passwords-check-message-holder").show();
			}
			
			//takyto login uz existuje !!!
			if(data.status == -3){
				$('#login-check-message-holder').show();
			}
			
			//takyto mail uz existuje !!!
			if(data.status == -4){
				$('#email-check-message-holder').show();
			}
			
			if(data.status == 1){
				$('#registration-form').fadeOut(function(){
					$('.reg-success-holder').fadeIn();
				});	
			}
			
			
		}, "json"
	);	
	return false;
}

function check_login(){
	$('#login-check-message-holder').hide();
	$.post(ROOT_PATH + "includes/modules/registration/ajax.php", {'check-login':1,'login':$('#registration-form input[name="login"]').val()},
		function(data){
			//neboli vyplnene vsetky polia
			if(data.status == 1){
				$('#login-check-message-holder').show();
			}
		}, "json"
	);		
}

function check_email(){
	$('#email-check-message-holder').hide();	
	$.post(ROOT_PATH + "includes/modules/registration/ajax.php", {'check-email':1,'email':$('#registration-form input[name="email"]').val()},
		function(data){
			//neboli vyplnene vsetky polia
			if(data.status == 1){
				$('#email-check-message-holder').show();
			}
		}, "json"
	);	
}

