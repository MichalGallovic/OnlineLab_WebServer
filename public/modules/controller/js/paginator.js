var numberOfPagesDisplay = 5;

$("document").ready(function() {
	$("table.controllers tbody").load(ROOT_PATH + "includes/modules/controller/loaddata.php?action=get_rows");
	
	$.get(ROOT_PATH + "includes/modules/controller/loaddata.php?action=row_count", function(data) {
		$("#page_count").val(Math.ceil(data / rows_per_page));
		generateRows(1);
	});

});

function generateRows(selected) {
	var pages = $("#page_count").val();
	
	if(pages <= 1){
		$("#paginator").remove();
	
	}else if (pages <= numberOfPagesDisplay) {
		
		var pagers = "<div id='paginator'>";
		
		for(i = 1; i <= Number(pages); i++){
			if (i == selected) {
					pagers += "<a href='#' class='pagor selected'>" + i + "</a>";
				} else {
					pagers += "<a href='#' class='pagor'>" + i + "</a>";
				}	
		}
		pagers += "<div style='clear:both;'></div></div>";
		$("#pager_holder").html(pagers);
		
		$(".pagor").click(function() {
			var index = $(".pagor").index(this);
			$("table.controllers tbody").load(ROOT_PATH + "includes/modules/controller/loaddata.php?action=get_rows&start=" + index);
			$(".pagor").removeClass("selected");
			$(this).addClass("selected");
		});		
	} else {
		if (selected < numberOfPagesDisplay) {
			// Draw the first 5 then have ... link to last
			var pagers = "<div id='paginator'>";
			for (i = 1; i <= numberOfPagesDisplay; i++) {
				if (i == selected) {
					pagers += "<a href='#' class='pagor selected'>" + i + "</a>";
				} else {
					pagers += "<a href='#' class='pagor'>" + i + "</a>";
				}				
			}
			pagers += "<div style='float:left;padding-left:4px;padding-right:10px;padding-top:18px;'>...</div><a href='#' class='pagor'>" + Number(pages) + "</a><div style='clear:both;'></div></div>";
			
			$("#paginator").remove();
			$("#pager_holder").html(pagers);
			$(".pagor").click(function() {
				updatePage(this);
			});
		} else if (selected > (Number(pages) - 4)) {
			// Draw ... link to first then have the last 5
			var pagers = "<div id='paginator'><a href='#' class='pagor'>1</a><div style='float:left;padding-left:4px;padding-right:10px;padding-top:18px;'>...</div>";
			for (i = (Number(pages) - 4); i <= Number(pages); i++) {
				if (i == selected) {
					pagers += "<a href='#' class='pagor selected'>" + i + "</a>";
				} else {
					pagers += "<a href='#' class='pagor'>" + i + "</a>";
				}				
			}			
			pagers += "<div style='clear:both;'></div></div>";
			
			$("#paginator").remove();
			$("#pager_holder").html(pagers);
			$(".pagor").click(function() {
				updatePage(this);
			});		
		} else {
			// Draw the number 1 element, then draw ... 2 before and two after and ... link to last
			var pagers = "<div id='paginator'><a href='#' class='pagor'>1</a><div style='float:left;padding-left:4px;padding-right:10px;padding-top:18px;'>...</div>";
			for (i = (Number(selected) - 2); i <= (Number(selected) + 2); i++) {
				if (i == selected) {
					pagers += "<a href='#' class='pagor selected'>" + i + "</a>";
				} else {
					pagers += "<a href='#' class='pagor'>" + i + "</a>";
				}
			}
			pagers += "<div style='float:left;padding-left:4px;padding-right:10px;padding-top:18px;'>...</div><a href='#' class='pagor'>" + pages + "</a><div style='clear:both;'></div></div>";
			
			$("#paginator").remove();
			$("#pager_holder").html(pagers);
			$(".pagor").click(function() {
				updatePage(this);
			});			
		}
	}
}

function updatePage(elem) {
	// Retrieve the number stored and position elements based on that number
	var selected = $(elem).text();

	// First update content
	$("table.controllers tbody").load(ROOT_PATH + "includes/modules/controller/loaddata.php?action=get_rows&start=" + (selected - 1));
	
	// Then update links
	generateRows(selected);
}