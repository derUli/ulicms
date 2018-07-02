<?php

$logger = LoggerRegistry::get("audit_log");

if (isset($_GET["destroy"]) or $_GET["action"] == "destroy") {
    db_query("UPDATE " . tbname("users") . " SET last_action = 0 WHERE id = " . $_SESSION["login_id"]);
    $url = apply_filter("index.php", "logout_url");
    header("Location: $url");
    session_destroy();
    exit();
}

if (isset($_REQUEST["reset_password_token"])) {
    $reset = new PasswordReset();
    $token = $reset->getToken($_REQUEST["reset_password_token"]);
    if ($token) {
        $user_id = $token->user_id;
        $user = new User($user_id);
        $user->setRequirePasswordChange(1);
        $user->save();
        register_session(getUserById($user_id));
        $token = $reset->deleteToken($_REQUEST["reset_password_token"]);
    } else {
        TextResult(get_translation("invalid_token"), 404);
    }
}

if (isset($_POST["login"])) {
    if (StringHelper::isNotNullOrWhitespace($_POST["system_language"])) {
        $_SESSION["system_language"] = basename($_POST["system_language"]);
    } else {
        $user = new User();
        $user->loadByUsername($_POST["user"]);
        $_SESSION["system_language"] = $user->getDefaultLanguage() ? $user->getDefaultLanguage() : Settings::get("system_language");
    }
    
    $confirmation_code = null;
    $twofactor_authentication = Settings::get("twofactor_authentication");
    
    if ($twofactor_authentication) {
        $confirmation_code = $_POST["confirmation_code"];
    }

    
    $sessionData = validate_login($_POST["user"], $_POST["password"], $confirmation_code);
    $sessionData = apply_filter($sessionData, "session_data");
    
    if ($sessionData) {
        // sync modules folder with database at first login
        if (! Settings::get("sys_initialized")) {
            clearCache();
            Settings::set("sys_initialized", "true");
        }
        do_event("login_ok");
		if($logger){
			$logger->debug("User {$_POST['user']} - Login OK");
		}
		register_session($sessionData, true);
    } else {
		if($logger){
			$logger->error("User {$_POST['user']} - Login Failed");
		}
        Response::sendStatusHeader(HttpStatusCode::UNAUTHORIZED);
		do_event ( "login_failed" );
    }
}
