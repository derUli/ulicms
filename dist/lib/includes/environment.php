<?php

declare(strict_types=1);
/**
 * Returns the version number of UliCMS Core
 * @return string
 */
function cms_version(): string
{
    $v = new UliCMSVersion();
    return implode('.', $v->getInternalVersion());
}

/**
 * Gets the UliCMS configuration environment
 * @return string
 */
function get_environment(): string
{
    return getenv("ULICMS_ENVIRONMENT") ?
            getenv("ULICMS_ENVIRONMENT") : "default";
}
