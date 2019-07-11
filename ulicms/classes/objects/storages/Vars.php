<?php

declare(strict_types=1);

class Vars {

    private static $vars = [];

    public static function get($var) {
        if (isset(self::$vars[$var])) {
            return self::$vars[$var];
        }
        return null;
    }

    public static function set($var, $val): void {
        self::$vars[$var] = $val;
    }

    public static function delete($var): void {
        if (isset(self::$vars[$var])) {
            unset(self::$vars[$var]);
        }
    }

    public static function clear(): void {
        self::$vars = [];
    }

    public static function getAllVars(): array {
        return self::$vars;
    }

}
