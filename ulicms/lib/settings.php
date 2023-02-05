<?php

declare(strict_types=1);

use UliCMS\Constants\AuditLog;

/**
 * Returns a setting
 * @param string $key
 * @deprecated since 2023.1
 * @return type
 */
function getconfig(string $key) {
    return Settings::get($key);
}

/**
 * @deprecated since 2023.1
 * Deletes a setting
 * @param string $key
 * @return bool
 */
// Remove an configuration variable
function deleteconfig(string $key): bool {
    return Settings::delete($key);
}

/**
 * 
 * Sets a setting
 * @param string $key
 * @param type $value
 * @deprecated since 2023.1
 */
function setconfig(string $key, $value) {
    $result = db_query("SELECT id FROM " . tbname("settings") .
            " WHERE name='$key'");
    if (db_num_rows($result) > 0) {
        db_query("UPDATE " . tbname("settings") . " SET value='$value' WHERE name='$key'");
    } else {
        db_query("INSERT INTO " . tbname("settings") .
                " (name, value) VALUES('$key', '$value')");
    }
    $logger = LoggerRegistry::get("audit_log");
    $userId = get_user_id();
    if ($logger) {
        if ($userId) {
            $user = getUserById($userId);
            $username = isset($user["username"]) ?
                    $user["username"] : AuditLog::UNKNOWN;
            $logger->debug("User $username - Changed setting $key to '$value'");
        } else {
            $username = AuditLog::UNKNOWN;
            $logger->debug("User $username - Changed setting $key to '$value'");
        }
    }
}

/**
 * Adds a setting if not exist yet
 * @param string $key
 * @param type $value
 * @deprecated since 2023.1
 * @return bool
 */
function initconfig(string $key, $value): bool {
    $success = false;
    if (!Settings::get($key)) {
        setconfig($key, $value);
        $success = true;
    }
    return $success;
}

function get_lang_config(string $name, string $lang): ?string {
    return Settings::getLang($name, $lang);
}
