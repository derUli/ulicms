<?php

class SessionManager extends Controller {

    public function login() {
        $logger = LoggerRegistry::get("audit_log");

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

        // if login successful register session
        if ($sessionData) {
            // sync modules folder with database at first login
            if (!Settings::get("sys_initialized")) {
                clearCache();
                Settings::set("sys_initialized", "true");
            }
            do_event("login_ok");
            if ($logger) {
                $logger->debug("User {$_POST['user']} - Login OK");
            }
            register_session($sessionData, true);
        } else {
            // If login failed
            if ($logger) {
                $logger->error("User {$_POST['user']} - Login Failed");
            }
            // send unauthorized header
            Response::sendStatusHeader(HttpStatusCode::UNAUTHORIZED);
            do_event("login_failed");
        }
    }

    public function logout() {
        $logger = LoggerRegistry::get("audit_log");

        $id = intval($_SESSION["login_id"]);
        if ($logger) {
            $user = getUserById($id);
            $name = isset($user["username"]) ? $user["username"] : AuditLog::UNKNOWN;
            $logger->debug("User $name - Logout");
        }
        // set user state to offline
        db_query("UPDATE " . tbname("users") . " SET last_action = 0 WHERE id = $id");
        $url = apply_filter("index.php", "logout_url");
        // throw the session to /dev/null
        session_destroy();
        // redirect to the logout Url
        Response::redirect($url, HttpStatusCode::MOVED_TEMPORARILY);
        exit();
    }

    public function resetPassword() {
        $logger = LoggerRegistry::get("audit_log");

        if (!isset($_REQUEST["token"])) {
            ExceptionResult("A token is required");
        }
        $reset = new PasswordReset();
        $token = $reset->getToken($_REQUEST["token"]);
        if ($token) {
            $user_id = $token->user_id;
            $user = new User($user_id);
            $user->setRequirePasswordChange(1);
            $user->save();
            register_session(getUserById($user_id));
            $token = $reset->deleteToken($_REQUEST["token"]);
            if ($logger) {
                $name = $user->getUsername() ? $user->getUsername() : AuditLog::UNKNOWN;
                $logger->debug("Password reset $name - OK");
            }
        } else {
            if ($logger) {
                $logger->error("Password reset - Invalid token");
            }
            TextResult(get_translation("invalid_token"), 404);
        }
    }

}
