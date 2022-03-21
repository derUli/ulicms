<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;
use UliCMS\Storages\Flags;

/**
 * Leert alle Caches
 * @return void
 */
function clearCache(): void {
    CacheUtil::clearCache();
}

/**
 * Deaktiviert den Seitencache, sofern aktiviert
 * @param type $do Wenn dies true ist, wird der Seitencache unabhängig von 
 * den Einstellungen für diese Session deaktiviert
 * @return void
 */
function no_cache(bool $do = false): void {
    if ($do) {
        Flags::setNoCache(true);
    } elseif (get_cache_control() === "auto"
            or get_cache_control() === "no_cache") {
        Flags::setNoCache(true);
    }
}
