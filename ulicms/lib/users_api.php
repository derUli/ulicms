<?php

use UliCMS\Security\Encryption;

// this ffile contains functions for managing user accounts
function getUsers() {
    $query = Database::query("SELECT id, username FROM " . tbname("users") . " ORDER by username");
    $users = [];
    while ($row = db_fetch_assoc($query)) {
        array_push($users, $row);
    }

    return $users;
}

function getAllUsers() {
    return getUsers();
}

function getUsersOnline() {
    $users_online = Database::query("SELECT username FROM " . tbname("users") . " WHERE last_action > " . (time() - 300) . " ORDER BY username");
    $retval = [];
    while ($row = db_fetch_object($users_online)) {
        $retval[] = $row->username;
    }
    return $retval;
}

function changePassword($password, $userId) {
    $user = new User($userId);
    if (!$user->getId()) {
        return false;
    }
    $user->setPassword($password);
    $user->save();
    return true;
}

function getUserByName($name) {
    $query = Database::query("SELECT * FROM " . tbname("users") . " WHERE username='" . Database::escapeValue($name, DB_TYPE_STRING) . "'");
    if (db_num_rows($query) > 0) {
        return db_fetch_assoc($query);
    } else {
        return false;
    }
}

function getUserById($id) {
    $query = Database::query("SELECT * FROM " . tbname("users") . " WHERE id = " . intval($id));
    if (db_num_rows($query) > 0) {
        return db_fetch_assoc($query);
    } else {
        return false;
    }
}

function get_user_id() {
    if (isset($_SESSION["login_id"])) {
        return intval($_SESSION["login_id"]);
    } else {
        return 0;
    }
}

function get_group_id() {
    if (isset($_SESSION["group_id"])) {
        return intval($_SESSION["group_id"]);
    } else {
        return 0;
    }
}

function user_exists($name) {
    $user = new User();
    $user->loadByUsername($name);
    return intval($user->getId()) > 0;
}

function register_session($user, $redirect = true) {
    $_SESSION["ulicms_login"] = $user["username"];
    $_SESSION["lastname"] = $user["lastname"];
    $_SESSION["firstname"] = $user["firstname"];
    $_SESSION["email"] = $user["email"];
    $_SESSION["login_id"] = $user["id"];
    $_SESSION["require_password_change"] = $user["require_password_change"];

    // Group ID
    $_SESSION["group_id"] = $user["group_id"];

    if (is_null($_SESSION["group_id"])) {
        $_SESSION["group_id"] = 0;
    }

    $_SESSION["logged_in"] = true;

    $_SESSION["session_begin"] = time();

    Database::query("UPDATE " . tbname("users") . " SET `last_login` = " . time() . " where id = " . $user["id"]);

    if (!$redirect) {
        return;
    }
    $login_url = apply_filter("index.php", "login_url");
    if (isset($_REQUEST["go"])) {
        Response::safeRedirect($_REQUEST["go"]);
    } else {
        $login_url = apply_filter("index.php", "login_url");
        Response::redirect($login_url);
    }

    return;
}

function validate_login($user, $password, $token = null) {
    $user = getUserByName($user);

    if ($user) {
        $password = Encryption::hashPassword($password);

        $twofactor_authentication = Settings::get("twofactor_authentication");
        if ($user["password"] == $password) {
            if ($twofactor_authentication and ! is_null($token)) {
                $ga = new PHPGangsta_GoogleAuthenticator();
                $ga_secret = Settings::get("ga_secret");
                $code = $ga->getCode($ga_secret);
                if ($code != $token) {
                    $_REQUEST["error"] = get_translation("confirmation_code_wrong");
                    return false;
                }
            }

            if ($user["locked"]) {
                $_REQUEST["error"] = get_translation("YOUR_ACCOUNT_IS_LOCKED");
                return false;
            }

            Database::query("update " . tbname("users") . " set `failed_logins` = 0 where id = " . intval($user["id"]));
            return $user;
        } else {
            // Limit Login Attampts
            $max_failed_logins_items = intval(Settings::get("max_failed_logins_items"));
            if ($max_failed_logins_items >= 1) {
                Database::query("update " . tbname("users") . " set `failed_logins` = `failed_logins` + 1 where id = " . intval($user["id"]));
                Database::query("update " . tbname("users") . " set `locked` = 1, `failed_logins` = 0 where `failed_logins` >= $max_failed_logins_items");
            }
        }
    }
    $_REQUEST["error"] = get_translation("USER_OR_PASSWORD_INCORRECT");
    return false;
}

// Ist der User eingeloggt
function is_logged_in() {
    return isset($_SESSION["logged_in"]);
}

// Alias für is_logged_in
function logged_in() {
    return is_logged_in();
}
