<?php

declare(strict_types=1);

namespace UliCMS\Storages;

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

class Flags {

    private static $noCache = false;

    public static function setNoCache(bool $bool): void {
        self::$noCache = boolval($bool);
    }

    public static function getNoCache(): bool {
        return self::$noCache;
    }

}
