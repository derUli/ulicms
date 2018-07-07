<?php

// get a config variable
function getconfig($key)
{
    if (SettingsCache::get($key)) {
        return SettingsCache::get($key);
    }
    $env_key = "ulicms_" . $key;
    $env_var = getenv($env_key);
    if ($env_var) {
        return $env_var;
    }
    $ikey = Database::escapeValue($key);
    $query = db_query("SELECT value FROM " . tbname("settings") . " WHERE name='$key'");
    if (db_num_rows($query) > 0) {
        while ($row = db_fetch_object($query)) {
            SettingsCache::set($key, $row->value);
            return $row->value;
        }
    } else {
        SettingsCache::set($key, null);
        return false;
    }
}

// Remove an configuration variable
function deleteconfig($key)
{
    $key = db_escape($key);
    db_query("DELETE FROM " . tbname("settings") . " WHERE name='$key'");
    SettingsCache::set($key, null);
    return db_affected_rows() > 0;
}

// Set a configuration Variable;
function setconfig($key, $value)
{
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
    SettingsCache::set($key, null);
}