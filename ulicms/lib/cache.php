<?php

declare(strict_types=1);

defined('ULICMS_ROOT') or exit('no direct script access allowed');

use UliCMS\Utils\CacheUtil;

/**
 * Clears all caches
 * @deprecated since version 2023.1
 * @return void
 */
function clearCache(): void {
    CacheUtil::clearCache();
}

/**
 * Disable Cache
 * @param bool $do
 * @return void
 */
function no_cache(bool $do = false): void {
    if ($do) {
        Vars::setNoCache(true);
    } elseif (in_array(get_cache_control(), ["auto", "no_cache"])) {
        Vars::setNoCache(true);
    }
}
