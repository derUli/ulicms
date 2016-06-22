$(document).ready(function() {

	$("form#database-login").on("submit", function(e) {
		e.preventDefault();
		var data = {
			servername : $("input[name='servername']").val(),
			loginname : $("input[name='loginname']").val(),
			passwort : $("input[name='passwort']").val(),
			datenbank : $("input[name='datenbank']").val(),
		}

		$("#error-message").hide();
		$.post("try_connect.php", data, function(text, status) {
			$("#error-message").html(text);
			if (text.length <= 0) {
				$("form#database-login").off('submit');
				$("form#database-login").submit();
				return true;
			} else {
				$("#error-message").slideDown();
			}
		});
	});

});