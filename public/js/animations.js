$( document ).ready( function() {
  $( "a" ).on( "click", function( e ) {
  		if($(this).find(".glyphicon-refresh").length > 0) {
  			var $icon = $(this).find(".glyphicon-refresh");
  			  animateClass = "glyphicon-refresh-animate";

  			$icon.addClass( animateClass );
  		} else if($(this).find(".glyphicon-sort").length > 0) {
  			var $icon = $(this).find(".glyphicon-sort");
  			  animateClass = "glyphicon-refresh-animate glyphicon-refresh";
  			$icon.removeClass("glyphicon-sort");
  			$icon.addClass( animateClass );
  		}
    });    
});