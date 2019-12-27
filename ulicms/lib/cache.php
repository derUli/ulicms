<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;

// Alternative PHP Cache leeren, sofern installiert und aktiv
function clearAPCCache(): bool {
    if (!function_exists("apc_clear_cache")) {
        return false;
    }
    apc_clear_cache();
    apc_clear_cache('user');
    apc_clear_cache('opcode');
    return true;
}

// Alle Caches leeren
// Sowohl den Seiten-Cache, den Download/Paketmanager Cache
// als auch den APC Bytecode Cache
function clearCache(): void {
    CacheUtil::clearCache();
}

function no_cache($do = false): void {
    if ($do) {
        Flags::setNoCache(true);
    } else if (get_cache_control() == "auto"
            or get_cache_control() == "no_cache") {
        Flags::setNoCache(true);
    }
}