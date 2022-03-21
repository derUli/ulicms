<?php

declare(strict_types=1);

namespace UliCMS\Storages;

class Flags {

    private static $noCache = false;

    public static function setNoCache(bool $bool): void {
        self::$noCache = boolval($bool);
    }

    public static function getNoCache(): bool {
        return self::$noCache;
    }

}
