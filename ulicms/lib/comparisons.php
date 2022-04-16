<?php

declare(strict_types=1);

use Carbon\Carbon;
use UliCMS\Models\Users\User;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

/**
 * Replacement for in_array with better performance
 * @param type $needle Needle
 * @param type $haystack Haystack
 * @return bool Needl is in Haystack
 */
function faster_in_array($needle, $haystack): bool {
    if (!is_array($haystack)) {
        return false;
    }

    $flipped = array_flip($haystack);
    return isset($flipped[$needle]);
}

// is $val a decimal number or a integer?
function is_decimal($val): bool {
    return is_numeric($val) && !ctype_digit(strval($val));
}

function is_today($datetime = null): bool {
    $carbon = get_carbon($datetime);
    return $carbon->isToday();
}

function midnight($datetime = null) {
    $carbon = get_carbon($datetime);
    $midnight = $carbon->startOfDay();
    return $midnight->getTimestamp();
}

function midday($datetime = null) {
    $carbon = get_carbon($datetime);
    $mdiday = $carbon->midday();
    return $mdiday->getTimestamp();
}

function end_of_day($datetime = null) {
    $carbon = get_carbon($datetime);
    $endOfDay = $carbon->endOfDay();
    return $endOfDay->getTimestamp();
}

function is_midnight($datetime = null): bool {
    $carbon = get_carbon($datetime);
    return $carbon->isStartOfDay();
}

function is_end_of_day($datetime = null): bool {
    $carbon = get_carbon($datetime);
    return $carbon->isEndOfDay();
}

function is_midday($datetime = null): bool {
    $carbon = get_carbon($datetime);
    return $carbon->isMidday();
}

function is_tomorrow($datetime = null): bool {
    $carbon = get_carbon($datetime);
    return $carbon->isTomorrow();
}

function is_yesterday($datetime = null): bool {
    $carbon = get_carbon($datetime);
    return $carbon->isYesterday();
}

function is_past($datetime = null): bool {
    $carbon = get_carbon($datetime);
    return $carbon->isPast();
}

function is_future($datetime = null): bool {
    $carbon = get_carbon($datetime);
    return $carbon->isFuture();
}

function get_carbon($datetime = null): Carbon {
    return new Carbon($datetime ?? time(), date_default_timezone_get());
}

function is_blank($val = null): bool {
    return isset($val) && (is_string($val) &&
            StringHelper::isNullOrWhitespace($val)) ||
            empty($val);
}

function is_present($val = null): bool {
    return isset($val) && !is_blank($val);
}

/**
 * Checks if a string is valid JSON
 * @param string|null $str input string
 * @return bool
 */
function is_json(?string $str): bool {
    return !is_null($str) ? json_decode($str) != null : false;
}

/**
 * Checks if this is an array with only numeric values
 * @param type $var Input
 * @return bool Is Numeric Array
 */
function is_numeric_array($var): bool {
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
 * Is the current working directory the admin directory?
 * @return bool Is Admin Directory
 */
function is_admin_dir(): bool {
    return basename(getcwd()) === "admin";
}

/**
 * Checks if the client is a desktop computer
 * @return bool Is Desktop
 */
function is_desktop(): bool {
    return !is_mobile();
}

/**
 * Checks if the client is a crawler.
 * Needs CrawlerDetect or another crawler detection module installed.
 * @param string|null $useragent Useragent
 * @return bool
 */
function is_crawler(?string $useragent = null): bool {
    $CrawlerDetect = new CrawlerDetect();
    return $CrawlerDetect->isCrawler($useragent);
}

// 21. Februar 2015
// Nutzt nun die Klasse Mobile_Detect
function is_mobile(): bool {

    $detect = new Mobile_Detect();
    $result = $detect->isMobile();

    if (Settings::get("no_mobile_design_on_tablet") && $result && $detect->isTablet()) {
        $result = false;
    }

    if (function_exists("apply_filter")) {
        $result = apply_filter($result, "is_mobile");
    }

    return $result;
}

/**
 * Checks if the maintenance mode is enabled
 * @return bool
 */
function isMaintenanceMode(): bool {
    if (!is_string(Settings::get("maintenance_mode"))) {
        return false;
    }
    return (strtolower(Settings::get("maintenance_mode")) == "on" ||
            strtolower(Settings::get("maintenance_mode")) == "true" ||
            Settings::get("maintenance_mode") == "1");
}

/**
 * Checks if the client is a tablet
 * @return bool
 */
function is_tablet(): bool {
    $detect = new Mobile_Detect();
    $result = $detect->isTablet();

    return $result;
}

/**
 * Checks if the current user has the admin flag set
 * If this is the case, the user has full access without restrictions
 * to the system
 * @return bool
 */
function is_admin(): bool {
    $isAdmin = false;
    $user_id = get_user_id();
    if ($user_id) {
        $user = new User(get_user_id());
        $isAdmin = $user->isAdmin();
    }
    return $isAdmin;
}

/**
 * Check if it is night (current hour between 0 and 4 o'Clock AM)
 * @param int|null $time Timestamp
 * @return bool Is Night
 */
function is_night(?int $time = null): bool {
    $time = $time ? $time : time();
    $hour = (int) date("G", $time);
    return ($hour >= 0 && $hour <= 4);
}

/**
 * Checks if the debug mode is enabled
 * @return bool Debug mode enabled
 */
function is_debug_mode(): bool {
    $config = new CMSConfig();
    return (defined("ULICMS_DEBUG") && ULICMS_DEBUG) || (isset($config->debug) && $config->debug);
}

/**
 * Checks if the application is running from command line
 * @return bool Is running from command line
 */
function isCLI(): bool {
    return php_sapi_name() == "cli";
}

function startsWith(string $haystack, string $needle, bool $case = true): bool {
    if ($case) {
        return (strcmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
    }
    return (strcasecmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
}

function endsWith(string $haystack, string $needle, bool $case = true): bool {
    if ($case) {
        return (strcmp(
                        substr($haystack, strlen($haystack) - strlen($needle)),
                        $needle
                ) === 0);
    }
    return (strcasecmp(
                    substr($haystack, strlen($haystack) - strlen($needle)),
                    $needle
            ) === 0);
}

function var_is_type($var, $type, $required = false): bool {
    $methodName = "is_{$type}";

    if ($var === null || $var === "") {
        return !$required;
    }

    if (function_exists($methodName)) {
        return $methodName($var);
    }
    return false;
}

/**
 * Checks if a string is a valid version number
 * @param string|null $input
 * @return bool
 */
function is_version_number(?string $input): bool {
    return ($input && version_compare($input, '0.0.1', '>='));
}
