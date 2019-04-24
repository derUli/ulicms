<?php

class Flags {

    private static $no_cache = false;

    public static function setNoCache($bool) {
        self::$no_cache = boolval($bool);
    }

    public static function getNoCache() {
        return self::$no_cache;
    }

}
