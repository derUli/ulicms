<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

use App\Backend\UliCMSVersion;

/**
 * Returns the version number of UliCMS Core
 *
 * @return string
 */
function cms_version(): string {
    $v = new UliCMSVersion();
    return implode('.', $v->getInternalVersion());
}

/**
 * Gets the UliCMS configuration environment
 *
 * @return string
 */
function get_environment(): string {
    return getenv('APP_ENV') ?
            getenv('APP_ENV') : 'default';
}
