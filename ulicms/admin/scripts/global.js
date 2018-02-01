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

		// dynamically add class form-control to all form elements
		// Todo: add class to all HTML-Tags to make this code useless
	    $('input, select, textarea')
			.not("input[type=checkbox]")
			.not("input[type=radio]")
			.not("input[type=button]")
			.not("input[type=submit]")
			.not("input[type=reset]")
			.not("input[type=image]")
			.addClass('form-control');

// better select-boxes
$("select").select2();

});
