<?php

declare(strict_types=1);

use UliCMS\Constants\AuditLog;
use UliCMS\Utils\Users\PasswordReset;
use UliCMS\Utils\Session;
use UliCMS\Registries\LoggerRegistry;
use UliCMS\Constants\HttpStatusCode;

class SessionManager extends Controller {

    public function login(): void {
        $logger = LoggerRegistry::get("audit_log");

        $user = new User();
        $user->loadByUsername($_POST["user"]);

        if (StringHelper::isNotNullOrWhitespace($_POST["system_language"])) {
            $_SESSION["system_language"] = basename($_POST["system_language"]);
        } else {
            $_SESSION["system_language"] = $user->getDefaultLanguage() ? $user->getDefaultLanguage() : Settings::get("system_language");
        }

        $confirmation_code = null;
        $twofactor_authentication = Settings::get("twofactor_authentication");

        if ($twofactor_authentication) {
            $confirmation_code = $_POST["confirmation_code"];
        }

        // TODO:
        // * user $user->checkPassword() instead of validate_login()
        // * Implement Google Authenticator in
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

            register_session((int) $sessionData['id']);
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

    public function logout(): void {
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
        Session::sessionDestroy();
        // redirect to the logout Url
        Response::redirect($url, HttpStatusCode::MOVED_TEMPORARILY);
        exit();
    }

    public function resetPassword(): void {
        $logger = LoggerRegistry::get("audit_log");

        if (!isset($_REQUEST["token"])) {
            ExceptionResult("A token is required");
        }
        $reset = new PasswordReset();
        $token = $reset->getTokenByTokenString($_REQUEST["token"]);
        if ($token) {
            $userId = $token->user_id;
            $user = new User($userId);
            $user->setRequirePasswordChange(1);
            $user->save();
            $token = $reset->deleteToken($_REQUEST["token"]);
            if ($logger) {
                $name = $user->getUsername() ? $user->getUsername() : AuditLog::UNKNOWN;
                $logger->debug("Password reset $name - OK");
            }

            register_session((int) $userId);
        } else {
            if ($logger) {
                $logger->error("Password reset - Invalid token");
            }
            TextResult(get_translation("invalid_token"), 404);
        }
    }

}
