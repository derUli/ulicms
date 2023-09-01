<?php

declare(strict_types=1);

namespace App\Registries;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Utils\Logger;

class LoggerRegistry {
    private static $loggers = [];

    /**
     * Register Logger
     *
     * @param string $name
     * @param Logger $logger
     *
     * @return void
     */
    public static function register(string $name, Logger $logger): void {
        self::$loggers[$name] = $logger;
    }

    public static function getAll(): array {
        return self::$loggers;
    }

    public static function get(string $name): ?Logger {
        return (isset(self::$loggers[$name])) ? self::$loggers[$name] : null;
    }

    public static function unregister(string $name): void {
        if (isset(self::$loggers[$name])) {
            unset(self::$loggers[$name]);
        }
    }
}
