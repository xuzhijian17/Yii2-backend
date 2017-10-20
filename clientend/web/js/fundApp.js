$(document).ready(function(){
	$("ul").each(function(){
		$(this).children("li:first").addClass("first");
		$(this).children("li:last-child").addClass("last");
	});
});
