$(function() {
	$("#menu-toggle").click(function() {
		$(".mainmenu").slideToggle();
	});
	$("#menu-clear-cache").click(function() {
		$(this).hide();
		$("#menu-clear-cache-loading").show()
		var url = $("#menu-clear-cache").data("url");
		$.get("index.php?clear_cache=clear_cache", function(result) {
			$("#menu-clear-cache").show();
			$("#menu-clear-cache-loading").hide();
		});
	});
});
