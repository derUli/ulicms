<?php

declare(strict_types=1);

namespace App\Storages;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

class Vars extends Storage {
    /**
     * @var bool
     */
    private static bool $noCache = false;

    /**
     * Set no cache flag
     *
     * @param bool $bool
     * @return void
     */
    public static function setNoCache(bool $bool): void {
        self::$noCache = $bool;
    }

    /**
     * Get no cache flag
     *
     * @return bool
     */
    public static function getNoCache(): bool {
        return self::$noCache;
    }
}
