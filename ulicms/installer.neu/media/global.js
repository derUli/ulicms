$("select#language").change(function() {
	var language = $("select#language").val();
	window.location.replace("index.php?step=1&language=" + language);
});
$("form#database-login").on("submit", function(e) {
	e.preventDefault();
	$("#loading").show();
	var data = {
		servername : $("input[name='mysql_host']").val(),
		loginname : $("input[name='mysql_user']").val(),
		passwort : $("input[name='mysql_password']").val(),
		datenbank : $("input[name='mysql_database']").val(),
	}

	$("#error-message").hide();
	$.post("try_connect.php", data, function(text, status) {
		$("#error-message").html(text);
		if (text.length <= 0) {
			$("form#database-login").off('submit');
			$("form#database-login").submit();
			return true;
		} else {
			$("#loading").hide();
			$("#error-message").slideDown();
		}

	});
	$("form.show-loading-indicator-on-submit").on("submit", function(e) {
		$("#loading").show();
	});

});