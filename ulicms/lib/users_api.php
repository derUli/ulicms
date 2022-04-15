<?php

declare(strict_types=1);

use UliCMS\Security\TwoFactorAuthentication;
use UliCMS\Models\Users\User;

/**
 * Returns all Users 
 * @return arrays of User ID, an Username
 */
function getUsers(): array {
    $users = [];
    $result = Database::query("SELECT id, username FROM " . tbname("users") .
                    " ORDER by username");
    while ($row = db_fetch_assoc($result)) {
        $users[] = $row;
    }

    return $users;
}

/**
 * Returns all Users 
 * @return arrays of User ID, an Username
 */
function getAllUsers(): array {
    return getUsers();
}

/**
 * Get online users
 * @return array Login names of currently logged in users
 */
function getUsersOnline(): array {
    $users_online = Database::query("SELECT username FROM " . tbname("users") . " WHERE last_action > " . (time() - 300) . " ORDER BY username");
    $retval = [];
    while ($row = db_fetch_object($users_online)) {
        $retval[] = $row->username;
    }
    return $retval;
}

/**
 * Change password of a user
 * @param type $password New plain text password
 * @param type $userId User id
 * @return boolean true if successful
 */
function changePassword($password, $userId) {
    $user = new User($userId);
    if (!$user->isPersistent()) {
        return false;
    }
    $user->setPassword($password);
    $user->save();
    return true;
}

/**
 * Get a user from database by it's username
 * @param string $name Username
 * @return array|null User dataset
 */
function getUserByName(string $name): ?array {
    $result = Database::query("SELECT * FROM " . tbname("users") .
                    " WHERE username='" . Database::escapeValue($name, DB_TYPE_STRING) . "'");
    if (db_num_rows($result) > 0) {
        return db_fetch_assoc($result);
    }
    return null;
}

/**
 * Get user by id
 * @param type $id User Id
 * @return array|null User as associative array
 */
function getUserById($id): ?array {
    $result = Database::query("SELECT * FROM " . tbname("users") .
                    " WHERE id = " . intval($id));
    if (db_num_rows($result) > 0) {
        return db_fetch_assoc($result);
    }
    return null;
}

/**
 * Get the user id of the current user
 * @return int
 */
function get_user_id(): int {
    if (isset($_SESSION["login_id"])) {
        return intval($_SESSION["login_id"]);
    } else {
        return 0;
    }
}

/**
 * Get the primary group id of the current user
 * @return int User Id, 0 if not set
 */
function get_group_id(): int {
    if (isset($_SESSION["group_id"])) {
        return intval($_SESSION["group_id"]);
    } else {
        return 0;
    }
}

/**
 * Checks if a user exists 
 * @param string $name Username
 * @return bool User exists
 */
function user_exists(string $name): bool {
    $user = new User();
    $user->loadByUsername($name);
    return intval($user->getId()) > 0;
}

/**
 * initiate user session
 * @param int $userId User Iid
 * @param bool $redirect should redirect after set fill $_SESSION
 * @return void
 */
function register_session(int $userId, bool $redirect = true): void {

    $userDataset = new User($userId);
    $userDataset->registerSession($redirect);
}

/**
 * Validate login data
 * @param string $username Username
 * @param string $password Plain password
 * @param string|null $confirmationCode Confirmation Code for Google Authenticator
 * @return array|null User or null
 */
function validate_login(
        string $username,
        string $password,
        ?string $confirmationCode = null
): ?array {
    $user = new User();
    $user->loadByUsername($username);

    $auth = new TwoFactorAuthentication();

    if ($user->isLocked()) {
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
            $user->setLocked($user->isLocked());
            $user->save();

            $_REQUEST["error"] = get_translation("YOUR_ACCOUNT_IS_LOCKED");
        }
        return null;
    }

    if (TwoFactorAuthentication::isEnabled() && !$auth->checkCode($confirmationCode)) {
        $_REQUEST["error"] = get_translation("confirmation_code_wrong");
        return null;
    }

    $user->setFailedLogins(0);
    $user->save();

    return getUserById($user->getId());
}

/**
 * Checks if the user is currently logged in.
 * @return bool Logged in
 */
function is_logged_in(): bool {
    return isset($_SESSION["logged_in"]);
}

/**
 * Checks if the user is currently logged in.
 * @return bool Logged in
 */
function logged_in(): bool {
    return is_logged_in();
}

/**
 * Get online users
 * @return array Login names of currently logged in users
 */
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

/**
 * Returns the preferred HTML editor of the current user.
 * @return string|null "ckeditor" or "codemirror"
 */
function get_html_editor(): ?string {
    $user_id = get_user_id();

    if (!$user_id) {
        return "ckeditor";
    }

    $user = new User($user_id);
    return $user->getHTMLEditor();
}
