$(function() {
	var language = $("html").data("select2-language");

	// toggle hamburger menu
	$("#menu-toggle").click(function() {
		$(".mainmenu").slideToggle();
	});
	// clear-cache shortcut icon
	$("#menu-clear-cache").click(function() {
		$(this).hide();
		$("#menu-clear-cache-loading").show()
		var url = $("#menu-clear-cache").data("url");
		$.get(url, function(result) {
			$("#menu-clear-cache").show();
			$("#menu-clear-cache-loading").hide();
		});
	});

	$(".tablesorter").DataTable({
		language : {
			url : $("body").data("datatables-translation")
		}
	});

	// dynamically add class form-control to all form elements to
	// make inputs prettier
	$('input, select, textarea').not("input[type=checkbox]").not(
			"input[type=radio]").not("input[type=button]").not(
			"input[type=submit]").not("input[type=reset]").not(
			"input[type=image]").addClass('form-control');

	// prettier select-boxes
	$("select").select2({
		width : '100%',
		language : $("html").data("select2-language")
	});
	$.datetimepicker.setLocale(language);

	$(".datepicker").datetimepicker({
		format : 'Y-m-d',
		timepicker : false
	});
	$("a.backend-menu-item-logout").click(function(event) {
		if (!window.confirm(MenuTranslation.Logout + "?")) {
			event.preventDefault();
		}

	});

});
