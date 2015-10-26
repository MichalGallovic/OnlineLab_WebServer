if(!window.ROOT_PATH)
	var ROOT_PATH = '/';

$(function() {
	$('.auth_types input:radio[name=account_type]').click(function() {
		var selectedAuthType = $('input:radio[name=account_type]:checked').val();
		$('div.infolabel').hide();
		$('#' + selectedAuthType + '-label' ).show();
		
	});
	
	//alert($('input:radio[name=account_type]:checked').val());
	
});		   