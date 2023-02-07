<?php

declare(strict_types=1);

class LoggerRegistry
{
    private static $loggers = [];

    public static function register(string $name, Logger $logger): void
    {
        self::$loggers[$name] = $logger;
    }

    public static function get(string $name): ?Logger
    {
        return (isset(self::$loggers[$name])) ? self::$loggers[$name] : null;
    }

    public static function unregister(string $name): void
    {
        if (isset(self::$loggers[$name])) {
            unset(self::$loggers[$name]);
        }
    }
}
