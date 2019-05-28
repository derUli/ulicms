<?php

use UliCMS\Constants\AuditLog;

// get a config variable
function getconfig($key) {
    return Settings::get($key);
}

// Remove an configuration variable
function deleteconfig($key) {
    return Settings::delete($key);
}

// Set a configuration Variable;
function setconfig($key, $value) {
    $query = db_query("SELECT id FROM " . tbname("settings") . " WHERE name='$key'");
    if (db_num_rows($query) > 0) {
        db_query("UPDATE " . tbname("settings") . " SET value='$value' WHERE name='$key'");
    } else {
        db_query("INSERT INTO " . tbname("settings") . " (name, value) VALUES('$key', '$value')");
    }
    $logger = LoggerRegistry::get("audit_log");
    $userId = get_user_id();
    if ($logger) {
        if ($userId) {
            $user = getUserById($userId);
            $username = isset($user["username"]) ? $user["username"] : AuditLog::UNKNOWN;
            $logger->debug("User $username - Changed setting $key to '$value'");
        } else {
            $username = AuditLog::UNKNOWN;
            $logger->debug("User $username - Changed setting $key to '$value'");
        }
    }
    SettingsCache::set($key, $value);
}

function initconfig($key, $value) {
    $retval = false;
    if (!Settings::get($key)) {
        setconfig($key, $value);
        $retval = true;
        SettingsCache::set($key, $value);
    }
    return $retval;
}
