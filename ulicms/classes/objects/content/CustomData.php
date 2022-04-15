<?php

declare(strict_types=1);

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

// This class contains methods to handle CustomData which is saved
// as json object in the "content" database table
class CustomData {

    private static $defaults = [];

    public static function get(?string $page = null): array {
        if (!$page) {
            $page = get_slug();
        }

        $language = getCurrentLanguage();

        $sql = "SELECT `custom_data` FROM " . tbname("content") .
                " WHERE slug='" . Database::escapeValue($page) .
                "' AND language='" .
                Database::escapeValue($language) . "'";
        $result = Database::query($sql);
        if (Database::getNumRows($result) > 0) {
            $dataset = Database::fetchObject($result);
            return is_json($dataset->custom_data) ? json_decode($dataset->custom_data, true) : [];
        }
        return [];
    }

    public static function set(string $var, $value, ?string $page = null): void {
        if (!$page) {
            $page = get_slug();
        }
        $data = self::get($page);
        $data[$var] = $value;

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        Database::query("UPDATE " . tbname("content") .
                        " SET custom_data = '" .
                        Database::escapeValue($json) .
                        "' WHERE slug='" . Database::escapeValue($page) . "'") .
                "' AND language='" .
                Database::escapeValue($_SESSION["language"]) . "'";
    }

    public static function delete(
            ?string $var = null,
            ?string $page = null
    )
    : void {
        if (!$page) {
            $page = get_slug();
        }

        $data = self::get($page);
        if (is_null($data) || !$var) {
            $data = [];
        }
        // Wenn $var gesetzt ist, nur $var aus custom_data löschen
        if ($var and isset($data[$var])) {
            unset($data[$var]);
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        Database::query("UPDATE " . tbname("content") . " SET custom_data = '"
                . Database::escapeValue($json)
                . "' WHERE slug='" . Database::escapeValue($page) . "' "
                . "AND language='" .
                Database::escapeValue($_SESSION["language"]) . "'");
    }

    public static function getCustomDataOrSetting(string $name) {
        $data = CustomData::get();
        if (!is_null($data) and is_array($data) and isset($data[$name])) {
            return $data[$name];
        }
        return Settings::get($name);
    }

    public static function setDefault(string $key, $value): void {
        self::$defaults[$key] = $value;
    }

    public static function getDefault(string $key) {
        if (!isset(self::$defaults[$key])) {
            return null;
        }
        return self::$defaults[$key];
    }

    public static function getDefaultJSON(): string {
        return json_readable_encode(self::$defaults);
    }

}
