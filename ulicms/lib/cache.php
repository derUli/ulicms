<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;

// Alle Caches leeren
// Sowohl den Seiten-Cache, den Download/Paketmanager Cache
// als auch den APC Bytecode Cache
function clearCache(): void {
    CacheUtil::clearCache();
}

function no_cache($do = false): void {
    if ($do) {
        Flags::setNoCache(true);
    } elseif (in_array(get_cache_control(), ["auto", "no_cache"])) {
        Flags::setNoCache(true);
    }
}
