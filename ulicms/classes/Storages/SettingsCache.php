<?php

declare(strict_types=1);

namespace UliCMS\Storages;

class SettingsCache {

    private static $settings = [];

    public static function get(string $key) {
        if (isset(self::$settings[$key])) {
            return self::$settings[$key];
        }
        return null;
    }

    public static function set(string $key, $value): void {
        if ($value === null and isset(self::$settings[$key])) {
            unset(self::$settings[$key]);
            return;
        }
        self::$settings[$key] = $value;
    }

}
