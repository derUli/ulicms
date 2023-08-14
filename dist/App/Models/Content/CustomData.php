<?php

declare(strict_types=1);

namespace App\Models\Content;

use Database;
use Settings;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

/**
 * This class parses custom data JSON
 */
class CustomData {
    /**
     * @var array<string, mixed>
     */
    private static array $defaults = [];

    /**
     * Get CustomData for current page
     *
     * @return array<string, mixed>
     */
    public static function get(): array {
        $page = get_slug();
        $language = getCurrentLanguage();

        $customData = [];

        $sql = 'SELECT `custom_data` FROM ' . Database::tableName('content') .
                " WHERE slug='" . Database::escapeValue($page) .
                "' AND language='" .
                Database::escapeValue($language) . "'";
        $result = Database::query($sql);

        if (Database::getNumRows($result) > 0) {
            $dataset = Database::fetchObject($result);
            $customData = isset($dataset->custom_data) && is_json($dataset->custom_data) ? json_decode($dataset->custom_data, true) : [];
        }

        return (array)$customData;
    }

    /**
     * Set CustomData attribute for current page
     *
     * @param string $var
     * @param mixed $value
     *
     * @return void
     */
    public static function set(string $var, mixed $value): void {
        $page = get_slug();

        $data = self::get();
        $data[$var] = $value;

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        Database::query('UPDATE ' . Database::tableName('content') .
                        " SET custom_data = '" .
                        Database::escapeValue($json) .
                        "' WHERE slug='" . Database::escapeValue($page) . "'") .
                "' AND language='" .
                Database::escapeValue($_SESSION['language']) . "'";
    }

    /**
     * Remove CustomData attribute from current page
     *
     * @param ?string $var
     *
     * @return void
     */
    public static function delete(
        ?string $var = null
    ): void {
        $page = get_slug();

        $data = self::get();

        // Wenn $var gesetzt ist, nur $var aus custom_data löschen
        if ($var && isset($data[$var])) {
            unset($data[$var]);
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        Database::query('UPDATE ' . Database::tableName('content') . " SET custom_data = '"
                . Database::escapeValue($json)
                . "' WHERE slug='" . Database::escapeValue($page) . "' "
                . "AND language='" .
                Database::escapeValue($_SESSION['language']) . "'");
    }

    public static function getCustomDataOrSetting(string $name): mixed {
        $data = self::get();

        if (is_array($data) && isset($data[$name])) {
            return $data[$name];
        }

        return Settings::get($name);
    }

    public static function setDefault(string $key, mixed $value): void {
        self::$defaults[$key] = $value;
    }

    public static function getDefault(string $key): mixed {
        if (! isset(self::$defaults[$key])) {
            return null;
        }

        return self::$defaults[$key];
    }

    /**
     * Get default JSON
     *
     * @return string
     */
    public static function getDefaultJSON(): string {
        return json_readable_encode(self::$defaults);
    }
}
