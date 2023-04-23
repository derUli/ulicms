<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Models\Users\PasswordReset;
use App\Utils\CacheUtil;

class SessionManager extends \App\Controllers\Controller {
    public function login(): void {
        $user = new User();
        $user->loadByUsername($_POST['user']);

        if (! empty($_POST['system_language'])) {
            $_SESSION['system_language'] = basename($_POST['system_language']);
        } else {
            $_SESSION['system_language'] = $user->getDefaultLanguage() ?: Settings::get('system_language');
        }

        $confirmation_code = null;
        $twofactor_authentication = Settings::get('twofactor_authentication');

        if ($twofactor_authentication) {
            $confirmation_code = $_POST['confirmation_code'];
        }

        // TODO:
        // * user $user->checkPassword() instead of validate_login()
        // * Implement Google Authenticator in
        $sessionData = validate_login($_POST['user'], $_POST['password'], $confirmation_code);
        $sessionData = apply_filter($sessionData, 'session_data');

        // if login successful register session
        if ($sessionData) {
            // sync modules folder with database at first login
            if (! Settings::get('sys_initialized')) {
                CacheUtil::clearCache();
                Settings::set('sys_initialized', 'true');
            }
            do_event('login_ok');
            register_session($sessionData);
        } else {
            // If login failed
            // send unauthorized header
            Response::sendStatusHeader(HttpStatusCode::UNAUTHORIZED);
            do_event('login_failed');
        }
    }

    public function logout(): void {
        $id = $_SESSION['login_id'];

        // set user state to offline
        db_query('UPDATE ' . tbname('users') . " SET last_action = 0 WHERE id = {$id}");
        $url = apply_filter('index.php', 'logout_url');
        // throw the session to /dev/null
        App\Utils\Session\sessionDestroy();
        // redirect to the logout Url
        Response::redirect($url, HttpStatusCode::MOVED_TEMPORARILY);
        exit();
    }

    public function resetPassword(): void {
        if (! isset($_REQUEST['token'])) {
            ExceptionResult('A token is required');
        }
        $reset = new PasswordReset();
        $token = $reset->getTokenByTokenString($_REQUEST['token']);
        if ($token) {
            $user_id = $token->user_id;
            $user = new User((int)$user_id);
            $user->setRequirePasswordChange(1);
            $user->save();
            $token = $reset->deleteToken($_REQUEST['token']);
            register_session(getUserById($user_id));
        } else {
            TextResult(get_translation('invalid_token'), 404);
        }
    }
}
