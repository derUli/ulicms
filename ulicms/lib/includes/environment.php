<?php

declare(strict_types=1);

defined('ULICMS_ROOT') or exit('no direct script access allowed');

/**
 * Returns the version number of UliCMS Core
 * @return string
 */
function cms_version(): string {
    $v = new UliCMSVersion();
    return implode(".", $v->getInternalVersion());
}

/**
 * Gets the UliCMS configuration environment
 * @return string
 */
function get_environment(): string {
    return getenv("ULICMS_ENVIRONMENT") ?
            getenv("ULICMS_ENVIRONMENT") : "default";
}

/**
 * Checks if a PHP builtin method is enabled
 * This returns true for any method that isn't specified
 * in the "disable_functions" option of php.ini
 * @param string $func method name
 * @return bool
 */
function func_enabled(string $func): bool {
    $disabledFunctions = explode(',', ini_get('disable_functions') ?? '');
    $disabledFunctions = array_map('trim', $disabledFunctions);

    return !in_array($func, $disabledFunctions);
}
