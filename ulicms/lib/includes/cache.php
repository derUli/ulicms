<?php

declare(strict_types=1);

defined('ULICMS_ROOT') or exit('no direct script access allowed');

/**
 * Disable Cache
 * @param bool $do
 * @return void
 */
function no_cache(bool $do = false): void
{
    if ($do) {
        Vars::setNoCache(true);
    } elseif (in_array(get_cache_control(), ["auto", "no_cache"])) {
        Vars::setNoCache(true);
    }
}
