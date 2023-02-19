<?php

declare(strict_types=1);

defined('ULICMS_ROOT') or exit('no direct script access allowed');

use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Detection\MobileDetect;

/**
 * Checks if a variable is a decimal number
 * @param type $val
 * @return bool
 */
function is_decimal($val): bool
{
    return is_numeric($val) && !ctype_digit((string) $val);
}

/**
 * Checks if a string is valid JSON
 * @param string|null $str
 * @return bool
 */
function is_json(?string $str): bool
{
    return $str ? json_decode($str) != null : false;
}

function is_numeric_array($var): bool
{
    if (!is_array($var)) {
        return false;
    }

    foreach ($var as $key => $value) {
        if (!is_numeric($value)) {
            return false;
        }
    }
    return true;
}

/**
 * Checks if we are currently in admin dir
 * @return bool
 */
function is_admin_dir(): bool
{
    return basename(getcwd()) === 'admin';
}

/**
 * Checks by useragent if the client is a desktop computer
 * @return bool
 */
function is_desktop(): bool
{
    return !is_mobile();
}

/**
 * Checks by useragent if the client is a crawler
 * @param string|null $useragent
 * @return bool
 */
function is_crawler(?string $useragent = null): bool
{
    $useragent = $useragent ?? Request::getUserAgent();

    $crawlerDetect = new CrawlerDetect();
    return $crawlerDetect->isCrawler($useragent);
}

/**
 * Checks by useragent if the current client is a mobile device
 * @return bool
 */
function is_mobile(): bool
{
    $mobileDetect = new MobileDetect();
    $result = $mobileDetect->isMobile();

    if (Settings::get("no_mobile_design_on_tablet") &&
            $result &&
            $mobileDetect->isTablet()) {
        $result = false;
    }

    return $result;
}

/**
 * Checks if the website is currently on maintenance mode
 * @return bool
 */
function isMaintenanceMode(): bool
{
    if (!is_string(Settings::get('maintenance_mode'))) {
        return false;
    }
    return (strtolower(Settings::get('maintenance_mode')) == "on" ||
            strtolower(Settings::get('maintenance_mode')) == "true" ||
            Settings::get('maintenance_mode') == "1");
}

/**
 * Checks by user agent if the current client is a tablet
 * @return bool
 */
function is_tablet(): bool
{
    $mobileDetect = new \Detection\MobileDetect();
    $result = $mobileDetect->isTablet();

    return $result;
}

/**
 * Checks if the script is run from command line
 * @return bool
 */
function is_cli(): bool
{
    return php_sapi_name() == 'cli';
}

/**
 * Checks if var has a type
 * @param type $var
 * @param type $type
 * @param type $required
 * @return bool
 */
function var_is_type($var, $type, $required = false): bool
{
    $methodName = "is_{$type}";

    if ($var === null || $var === '') {
        return !$required;
    }

    if (function_exists($methodName)) {
        return $methodName($var);
    }
    return false;
}

/**
 * Checks if $input is a valid version number
 * @param string|null $input
 * @return bool
 */
function is_version_number(?string $input): bool
{
    return ($input && version_compare($input, '0.0.1', '>='));
}