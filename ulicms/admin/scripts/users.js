$(function() {
	function validatePasswords(event) {

		var field1 = $("#admin_password");
		var field2 = $("#admin_password_repeat");

		var val1 = $(field1).val();
		var val2 = $(field2).val();

		if (val1 == "" && val2 == "") {
			$(field1).css("background-color", "");
			$(field2).css("background-color", "");
			$(field1).css("color", "");
			$(field2).css("color", "");
		} else if (val1 == val2) {
			$(field1).css("background-color", "green");
			$(field2).css("background-color", "green");
			$(field1).css("color", "white");
			$(field2).css("color", "white");
		} else {
			$(field1).css("background-color", "red");
			$(field2).css("background-color", "red");
			$(field1).css("color", "white");
			$(field2).css("color", "white");
		}
	}

	function submitPasswordForm(event) {
		event.preventDefault();
		validatePasswords(event);
		if ($("#admin_password").css("background-color") != "red") {
			$("form#edit_user").off("submit");
			$("form#edit_user").submit();
		} else {
			$("#admin_password").focus();
		}

	}

	$("#admin_password").keyup(validatePasswords);
	$("#admin_password_repeat").keyup(validatePasswords);
	$("form#edit_user").on("submit", submitPasswordForm);

});