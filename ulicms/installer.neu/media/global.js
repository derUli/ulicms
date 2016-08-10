$.ajaxSetup({
	cache : false
});

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
		mysql_prefix : $("input[name='mysql_prefix']").val()
	}
	$("#error-message").hide();
	$.post("index.php?submit_form=TryConnect", data, function(text, status) {
		$("#error-message").html(text);
		if (text.length <= 0) {
			location.replace("index.php?step=4")
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

String.prototype.contains = function(it) {
	return this.indexOf(it) != -1;
};

function installNextDBScript() {
	$.post("index.php?submit_form=Install", function(text, status) {
		if (text == "finish") {
			location.replace("index.php?step=7");
		} else if (text.contains("<!--ok-->")) {
			$("form#setup-database").html(text);
			installNextDBScript();
		} else {
			$("form#setup-database").html(text);
		}
	});
}

$("form#setup-database").on("submit", function(e) {
	e.preventDefault();
	installNextDBScript();
});

$("form#admin-login").on("submit", function(e) {
	e.preventDefault();
	var pass1 = $("#admin_password").val();
	var pass2 = $("#admin_password_repeat").val();
	if (pass1 == "") {
		alert("Password can not be empty.");
	}

	else if (pass1 != pass2) {
		alert("Passwords are not equal.");
	} else {
		$("form#admin-login").off("submit");
		$("form#admin-login").submit();
	}
});