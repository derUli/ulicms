/* global bootbox, UserTranslation */

// This file contains the code for the user profil edit page

// check if password field and password repeat are equal
// then colorize the inputs
function validatePasswords() {
    var field1 = $("#password");
    var field2 = $("#password_repeat");

    var val1 = $(field1).val();
    var val2 = $(field2).val();

    if (val1 && val2 && val1 !== val2) {
        // if the password fields are NOT equal then make the fields red
        $(field1).css("background-color", "red");
        $(field1).addClass("invalid");
        $(field2).css("background-color", "red");
        $(field1).css("color", "white");
        $(field2).css("color", "white");
    } else {
        // if the password fields are empty then unset background color
        // and text color
        $(field1).css("background-color", "");
        $(field1).removeClass("invalid");
        $(field2).css("background-color", "");
        $(field1).css("color", "inherit");
        $(field2).css("color", "inherit");
    }
}

function submitPasswordForm(event) {
    event.preventDefault();
    validatePasswords(event);
    if ($("#password").hasClass("invalid")) {
        bootbox.alert(UserTranslation.PasswordsNotEqual, function () {
            $("#password").focus();
        });
        return false;
    }
    $("form#edit_user").off("submit").submit();
}

$(function () {
    $("#password").on("blur", validatePasswords);
    $("#password_repeat").on("blur", validatePasswords);
    $("form#edit_user").on("submit", submitPasswordForm);
});