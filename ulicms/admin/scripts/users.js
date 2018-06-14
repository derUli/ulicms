// This file contains the code for the user profil edit page

// check if password field and password repeat are equal
// then colorize the inputs
function validatePasswords(event) {
	var field1 = $("#admin_password");
	var field2 = $("#admin_password_repeat");

	var val1 = $(field1).val();
	var val2 = $(field2).val();
	// if the password fields are empty then unset background color and text
	// color
	if (val1 == "" && val2 == "") {
		$(field1).css("background-color", "");
		$(field2).css("background-color", "");
		$(field1).css("color", "");
		$(field2).css("color", "");
		// if the password fields are equal then make the fields green
	} else if (val1 == val2) {
		$(field1).css("background-color", "green");
		$(field2).css("background-color", "green");
		$(field1).css("color", "white");
		$(field2).css("color", "white");
	} else {
		// if the password fields are NOT equal then make the fields red
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

$(function() {
	$("#admin_password").keyup(validatePasswords);
	$("#admin_password_repeat").keyup(validatePasswords);
	$("form#edit_user").on("submit", submitPasswordForm);
});