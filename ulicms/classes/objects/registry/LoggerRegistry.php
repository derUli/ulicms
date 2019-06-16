<?php

class LoggerRegistry {

    private static $loggers = array();

    public static function register($name, $logger) {
        self::$loggers[$name] = $logger;
    }

    public static function get($name) {
        return (isset(self::$loggers[$name])) ? self::$loggers[$name] : null;
    }

    public static function unregister($name) {
        if (isset(self::$loggers[$name])) {
            unset(self::$loggers[$name]);
        }
    }

}
