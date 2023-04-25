<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

use App\Security\TwoFactorAuthentication;

/**
 * Gets id and username of all users
 * @return array
 */
function getUsers(): array {
    $users = [];
    $result = Database::query('SELECT id, username FROM ' . Database::tableName('users') .
                    ' ORDER by username');
    while ($row = Database::fetchAssoc($result)) {
        $users[] = $row;
    }

    return $users;
}

/**
 * Gets the usernames of all users whose last action was less than 5 minutes ago
 * @return array
 */
function getUsersOnline(): array {
    $users_online = Database::query('SELECT username FROM ' . Database::tableName('users') . ' WHERE last_action > ' . (time() - 300) . ' ORDER BY username');
    $retval = [];
    while ($row = Database::fetchObject($users_online)) {
        $retval[] = $row->username;
    }
    return $retval;
}

/**
 * Changes the password of a user
 * @param string $password
 * @param int|null $userId
 * @return bool
 */
function changePassword(string $password, ?int $userId) {
    $user = new User($userId);
    if (! $user->isPersistent()) {
        return false;
    }
    $user->setPassword($password);
    $user->save();
    return true;
}

/**
 * Gets a user by username
 * @param string $name
 * @return array|null
 */
function getUserByName(string $name): ?array {
    $result = Database::query('SELECT * FROM ' . Database::tableName('users') .
                    " WHERE username='" . Database::escapeValue($name, DB_TYPE_STRING) . "'");
    if (Database::getNumRows($result) > 0) {
        return  Database::fetchAssoc($result);
    }
    return null;
}

/**
 * Get a user by id
 * @param int $id
 * @return array|null
 */
function getUserById(int $id): ?array {
    $result = Database::query('SELECT * FROM ' . Database::tableName('users') .
                    ' WHERE id = ' . (int)$id);
    if (Database::getNumRows($result) > 0) {
        return  Database::fetchAssoc($result);
    }
    return null;
}

/**
 * Gets the id of the currently logged in user or
 * @return int
 */
function get_user_id(): ?int {
    return $_SESSION['login_id'] ?? null;
}

/**
 * Gets the primary group id of the currentlogy logged in user
 * @return int|null
 */
function get_group_id(): ?int {
    return isset($_SESSION['group_id']) ? (int)$_SESSION['group_id'] : null;
}

/**
 * Checks if a user with the given username exists
 * @param string $name
 * @return bool
 */
function user_exists(string $name): bool {
    $user = new User();
    $user->loadByUsername($name);
    return $user->isPersistent();
}

/**
 * Registers a session for the given user
 * @param array $user
 * @param bool $redirect
 * @return void
 */
function register_session(array $user, bool $redirect = true): void {
    $userDataset = new User((int)$user['id']);
    $userDataset->registerSession($redirect);
}
/**
 * Validates a user login
 * @param string $username
 * @param string $password
 * @param string|null $token
 * @return array|null
 */
function validate_login(
    string $username,
    string $password,
    ?string $token = null
): ?array {
    $user = new User();
    $user->loadByUsername($username);

    $auth = new TwoFactorAuthentication();

    if ($user->isLocked()) {
        $_REQUEST['error'] = get_translation('YOUR_ACCOUNT_IS_LOCKED');
        return null;
    }

    if (! $user->isPersistent()) {
        $_REQUEST['error'] = get_translation('USER_OR_PASSWORD_INCORRECT');
        return null;
    }

    if (! $user->checkPassword($password)) {
        $_REQUEST['error'] = get_translation('USER_OR_PASSWORD_INCORRECT');

        // Limit Login Attampts
        $max_failed_logins_items = (int)Settings::get('max_failed_logins_items');
        $user->setFailedLogins($user->getFailedLogins() + 1);
        $user->save();

        if ($max_failed_logins_items >= 1
                && $user->getFailedLogins() >= $max_failed_logins_items) {
            $user->setLocked($user->isLocked());
            $user->save();

            $_REQUEST['error'] = get_translation('YOUR_ACCOUNT_IS_LOCKED');
        }
        return null;
    }

    if (TwoFactorAuthentication::isEnabled() && ! $auth->checkCode($token)) {
        $_REQUEST['error'] = get_translation('confirmation_code_wrong');
        return null;
    }

    $user->setFailedLogins(0);
    $user->save();

    return getUserById($user->getId());
}

/**
 * Checks if a user is logged in
 * @return bool
 */
function is_logged_in(): bool {
    return isset($_SESSION['logged_in']);
}

/**
 * Gets the configured HTML editor for the currently logged in user or default
 * @return string
 */
function get_html_editor(): string {
    $userId = get_user_id();

    $user = new User($userId);
    return $user->getHTMLEditor();
}
