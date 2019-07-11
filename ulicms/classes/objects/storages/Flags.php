<?php

declare(strict_types=1);

class Flags {

    private static $no_cache = false;

    public static function setNoCache(bool $bool): void {
        self::$no_cache = boolval($bool);
    }

    public static function getNoCache(): bool {
        return self::$no_cache;
    }

}
