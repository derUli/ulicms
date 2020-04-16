// Shake animation on failed login
$(() => {
    bindTogglePassword("#password", "#view_password");
    
    if ($("form#login-form").data("has-error")) {
        shake("form#login-form");
    }
});
