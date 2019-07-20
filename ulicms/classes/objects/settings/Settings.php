<?php

declare(strict_types=1);

use UliCMS\Constants\AuditLog;

class Settings {

    public static function register(string $key, $value, $type = 'str'): void {
        self::init($key, $value, $type);
    }

    public static function init(string $key, $value, string $type = 'str'): bool {
        if (!self::get($key)) {
            self::set($key, $value, $type);
            SettingsCache::set($key, $value);
            return true;
        }
        return false;
    }

    // get a config variable
    public static function get(string $key, string $type = 'str') {
        if (!is_null(SettingsCache::get($key))) {
            return SettingsCache::get($key);
        }
        $key = db_escape($key);
        $result = db_query("SELECT value FROM " . tbname("settings") . " WHERE name='$key'");
        if (db_num_rows($result) > 0) {
            while ($row = db_fetch_object($result)) {
                $value = self::convertVar($row->value, $type);
                SettingsCache::set($key, $value, $type);
                return $value;
            }
        }
        SettingsCache::set($key, null);
        return null;
    }

    public static function output(string $key, string $type = 'str'): void {
        $value = self::get($key, $type);
        if ($value) {
            echo $value;
        }
    }

    public static function outputEscaped(string $key,
            string $type = 'str'): void {
        $value = self::get($key, $type);
        if ($value) {
            esc($value);
        }
    }

    public static function getLanguageSetting(string$name,
            ?string $language = null, string $type = 'str') {
        $retval = false;
        $settingsName = $language ? "{$name}_{$language}" : $name;

        $config = self::get($settingsName);
        if ($config) {
            $retval = $config;
        } else {
            $config = self::get($name, $type);
        }
        return $config;
    }

    public static function getLang(string $name,
            ?string $language = null, string $type = 'str') {
        return self::getLanguageSetting($name, $language, $type);
    }

    public static function setLanguageSetting(string $name, $value,
            ?string $language = null): void {
        $settingsName = $language ? "{$name}_{$language}" : $name;

        if ($value) {
            Settings::set($settingsName, $value);
        } else {
            Settings::delete($settingsName);
        }
    }

    // Set a configuration Variable;
    public static function set(string $key, $value,
            string $type = 'str'): void {
        $key = db_escape($key);
        $originalValue = self::convertVar($value, $type);
        $value = db_escape($originalValue);
        $result = db_query("SELECT id FROM " . tbname("settings") . " WHERE name='$key'");
        if (db_num_rows($result) > 0) {
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
                $logger->debug("User $username - Changed setting $key to '$originalValue'");
            } else {
                $username = AuditLog::UNKNOWN;
                $logger->debug("User $username - Changed setting $key to '$originalValue'");
            }
        }
        SettingsCache::set($key, $originalValue);
    }

// Remove an configuration variable
    public static function delete(string $key): bool {
        $key = db_escape($key);
        db_query("DELETE FROM " . tbname("settings") . " WHERE name='$key'");
        SettingsCache::set($key, null);
        return db_affected_rows() > 0;
    }

    public static function convertVar($value, string $type) {
        switch ($type) {
            case 'str':
                $value = strval($value);
                break;
            case 'int':
                $value = intval($value);
                break;
            case 'float':
                $value = floatval($value);
                break;
            case 'bool':
                $value = intval(boolval($value));
                break;
        }
        return $value;
    }

    public static function getAll(string $order = "name"): array {
        $datasets = [];
        $result = Database::query("SELECT * FROM `{prefix}settings` order by $order", true);
        while ($dataset = Database::fetchObject($result)) {
            $datasets[] = $dataset;
        }
        return $datasets;
    }

    public static function mappingStringToArray(string $str): array {
        $str = trim($str);
        $str = normalizeLN($str, "\n");
        $lines = explode("\n", $str);
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines, 'strlen');
        $result = [];
        foreach ($lines as $line) {
// if a line starts with a hash skip it (comment)
            if (startsWith($line, "#")) {
                continue;
            }
            $splitted = explode("=>", $line);
            $splitted = array_map('trim', $splitted);
            $key = $splitted[0];
            $value = $splitted[1];
            $result[$key] = $value;
        }
        return $result;
    }

}
