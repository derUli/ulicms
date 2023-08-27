// Shake animation on failed login
$(() => {
    if ($("form#login-form").data("has-error")) {
        shake("form#login-form");
    }
});
