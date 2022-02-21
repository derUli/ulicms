<?php

declare(strict_types=1);

/**
 * Returns the display version number for UliCMS
 * @return string version number
 */
function cms_version(): string {
    $v = new UliCMSVersion();
    return implode(".", $v->getInternalVersion());
}

/**
 * Returns the current server environment from environment vars
 * @return string environment or default
 */
function get_environment(): string {
    return getenv("ULICMS_ENVIRONMENT") ?
            getenv("ULICMS_ENVIRONMENT") : "default";
}

/**
 * Checks if a method is enabled
 * @param string $func name of the function to check
 * Many hosts disable shell functions
 * @return bool true if this function isn't disabled
 */
function func_enabled(string $func): bool {
    $disabledFunctions = explode(',', ini_get('disable_functions'));
    $disabledFunctions = array_map('trim', $disabledFunctions);
    return !faster_in_array($func, $disabledFunctions);
}
