$(function() {
	$("#menu-toggle").click(function() {
		$(".mainmenu").slideToggle();
	});
	$("#menu-clear-cache").click(function() {
		$(this).hide();
		$("#menu-clear-cache-loading").show()
		var url = $("#menu-clear-cache").data("url");
		$.get(url, function(result) {
			$("#menu-clear-cache").show();
			$("#menu-clear-cache-loading").hide();
		});
	});
});
