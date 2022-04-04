<?php

declare(strict_types=1);

use UliCMS\Constants\AuditLog;
use UliCMS\Storages\SettingsCache;

// get a config variable
function getconfig(string $key) {
    return Settings::get($key);
}

// Remove an configuration variable
function deleteconfig(string $key): bool {
    return Settings::delete($key);
}

// Set a configuration Variable;
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
    SettingsCache::set($key, $value);
}

function initconfig(string $key, $value): bool {
    $retval = false;
    if (!Settings::get($key)) {
        setconfig($key, $value);
        $retval = true;
        SettingsCache::set($key, $value);
    }
    return $retval;
}

function get_lang_config(string $name, string $lang): ?string {
    $retval = null;
    $config = Settings::get($name . "_" . $lang);
    if ($config) {
        $retval = $config;
    } else {
        $config = Settings::get($name);
    }
    return $config ? $config : null;
}
