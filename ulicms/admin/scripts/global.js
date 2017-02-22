$(function() {
	$("#menu-toggle").click(function() {
		$(".mainmenu").slideToggle();
	});
	$("#menu-clear-cache").click(function() {
		$(this).attr("src", "gfx/loading2.gif");
		$.get("index.php?ajax_cmd=clear_cache", function(result) {
			$("#menu-clear-cache").attr("src", "gfx/clear-cache.png");
		});
	});
});
