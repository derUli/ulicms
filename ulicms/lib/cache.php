<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;

/**
 * Leert alle Caches
 * @return void
 */
function clearCache(): void
{
    CacheUtil::clearCache();
}

function no_cache($do = false): void
{
    if ($do) {
        Flags::setNoCache(true);
    } elseif (get_cache_control() == "auto"
            or get_cache_control() == "no_cache") {
        Flags::setNoCache(true);
    }
}
