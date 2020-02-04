<?php

declare(strict_types=1);

use UliCMS\Security\TwoFactorAuthentication;

// this file contains functions for managing user accounts
function getUsers(): array {
    $users = [];
    $result = Database::query("SELECT id, username FROM " . tbname("users") .
                    " ORDER by username");
    while ($row = db_fetch_assoc($result)) {
        $users[] = $row;
    }

    return $users;
}

function getAllUsers(): array {
    return getUsers();
}

function getUsersOnline(): array {
    $users_online = Database::query("SELECT username FROM " . tbname("users") . " WHERE last_action > " . (time() - 300) . " ORDER BY username");
    $retval = [];
    while ($row = db_fetch_object($users_online)) {
        $retval[] = $row->username;
    }
    return $retval;
}

function changePassword($password, $userId) {
    $user = new User($userId);
    if (!$user->isPersistent()) {
        return false;
    }
    $user->setPassword($password);
    $user->save();
    return true;
}

function getUserByName(string $name): ?array {
    $result = Database::query("SELECT * FROM " . tbname("users") .
                    " WHERE username='" . Database::escapeValue($name, DB_TYPE_STRING) . "'");
    if (db_num_rows($result) > 0) {
        return db_fetch_assoc($result);
    }
    return null;
}

function getUserById($id): ?array {
    $result = Database::query("SELECT * FROM " . tbname("users") .
                    " WHERE id = " . intval($id));
    if (db_num_rows($result) > 0) {
        return db_fetch_assoc($result);
    }
    return null;
}

function get_user_id(): int {
    if (isset($_SESSION["login_id"])) {
        return intval($_SESSION["login_id"]);
    } else {
        return 0;
    }
}

function get_group_id(): int {
    if (isset($_SESSION["group_id"])) {
        return intval($_SESSION["group_id"]);
    } else {
        return 0;
    }
}

function user_exists(string $name): bool {
    $user = new User();
    $user->loadByUsername($name);
    return intval($user->getId()) > 0;
}

function register_session(array $user, bool $redirect = true): void {
    $userDataset = new User($user["id"]);
    $userDataset->registerSession($redirect);
}

function validate_login(
        string $username,
        string $password,
        ?string $token = null
): ?array {
    $user = new User();
    $user->loadByUsername($username);

    $auth = new TwoFactorAuthentication();

    if ($user->getLocked()) {
        $_REQUEST["error"] = get_translation("YOUR_ACCOUNT_IS_LOCKED");
        return null;
    }

    if (!$user->isPersistent()) {
        $_REQUEST["error"] = get_translation("USER_OR_PASSWORD_INCORRECT");
        return null;
    }

    if (!$user->checkPassword($password)) {
        $_REQUEST["error"] = get_translation("USER_OR_PASSWORD_INCORRECT");

        // Limit Login Attampts
        $max_failed_logins_items = intval(
                Settings::get("max_failed_logins_items")
        );
        $user->setFailedLogins($user->getFailedLogins() + 1);
        $user->save();

        if ($max_failed_logins_items >= 1
                and $user->getFailedLogins() >= $max_failed_logins_items) {
            $user->setLocked($user->getLocked());
            $user->save();

            $_REQUEST["error"] = get_translation("YOUR_ACCOUNT_IS_LOCKED");
        }
        return null;
    }

    if (TwoFactorAuthentication::isEnabled() and ! $auth->checkCode($token)) {
        $_REQUEST["error"] = get_translation("confirmation_code_wrong");
        return null;
    }

    $user->setFailedLogins(0);
    $user->save();

    return getUserById($user->getId());
}

// Ist der User eingeloggt
function is_logged_in(): bool {
    return isset($_SESSION["logged_in"]);
}

// Alias für is_logged_in
function logged_in(): bool {
    return is_logged_in();
}

function getOnlineUsers(): array {
    return getUsersOnline();
}

/**
 * Gravatar support was removed due to legal issues with the GDPR.
 * This method is still called get_gravatar() for compatiblity reasons
 * If there is a registered user with the email address returns his avatar image file
 * else returns the no avatar placeholder image
 *
 */
function get_gravatar(
        string $email,
        int $s = 80,
        string $d = 'mm',
        string $r = 'g',
        bool $img = false,
        array $atts = []
): string {
    //
    $url = ModuleHelper::getBaseUrl("/admin/gfx/no_avatar.png");

    // If there is a user with this email address return it's avatar
    $user = new User();
    $user->loadByEmail($email);
    if ($user->hasProcessedAvatar()) {
        $url = $user->getAvatar();
    }

    $html = "";
    if ($img) {
        $html = '<img src="' . $url . '"';
        foreach ($atts as $key => $val) {
            $html .= ' ' . $key . '="' . $val . '"';
        }
        $html .= ' />';
    }
    return $img ? $html : $url;
}

// Gibt den für den derzeit eingeloggten User eingestellten HTML-Editor aus.
// Wenn der Anwender nicht eingeloggt ist return null
function get_html_editor(): ?string {
    $user_id = get_user_id();

    if (!$user_id) {
        return "ckeditor";
    }

    $user = new User($user_id);
    return $user->getHTMLEditor();
}
