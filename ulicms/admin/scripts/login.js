
$(document).ready(function () {
    bindTogglePassword("#password", "#view_password")
});

// Shake animation on failed login
$(function () {
    if ($("form#login-form").data("has-error")) {
        shake("form#login-form");
    }
});