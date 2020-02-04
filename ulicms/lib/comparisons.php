<?php

declare(strict_types=1);

use Carbon\Carbon;

function faster_in_array($needle, $haystack): bool {
    if (!is_array($haystack)) {
        return false;
    }
    $flipped = array_flip($haystack);
    return isset($flipped[$needle]);
}

// is $val a decimal number or a integer?
function is_decimal($val): bool {
    return is_numeric($val) and ! ctype_digit(strval($val));
}

function is_zero($val): bool {
    return is_numeric($val) && $val == 0;
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

function is_json(?string $str): bool {
    return !is_null($str) ? json_decode($str) != null : false;
}

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

function is_true($var): bool {
    return (isset($var) and $var);
}

function is_false($var): bool {
    return !(isset($var) and $var);
}

// sind wir gerade im Adminordner?
function is_admin_dir(): bool {
    return basename(getcwd()) === "admin";
}

function is_desktop(): bool {
    return !is_mobile();
}

function is_crawler(?string $useragent = null): bool {
    if (is_null($useragent)) {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
    }
    $isCrawler = apply_filter($useragent, "is_crawler");
    if (is_bool($isCrawler) or is_int($isCrawler)) {
        return boolval($isCrawler);
    }

    $crawlers = 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|' .
            'AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|' .
            'GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot'
            . '|eStyle|Scrubby';
    $isCrawler = (preg_match("/$crawlers/", $useragent) > 0);
    return $isCrawler;
}

// 21. Februar 2015
// Nutzt nun die Klasse Mobile_Detect
function is_mobile(): bool {
    $result = false;
    
    if (class_exists("Mobile_Detect")) {
        $detect = new Mobile_Detect();
        $result = $detect->isMobile();
    }
    
    if (Settings::get("no_mobile_design_on_tablet")
            and $result and $detect->isTablet()) {
        $result = false;
    }
    
    if (function_exists("apply_filter")) {
        $result = apply_filter($result, "is_mobile");
    }
    
    return $result;
}

function isMaintenanceMode(): bool {
    if (!is_string(Settings::get("maintenance_mode"))) {
        return false;
    }
    return (strtolower(Settings::get("maintenance_mode")) == "on" ||
            strtolower(Settings::get("maintenance_mode")) == "true" ||
            Settings::get("maintenance_mode") == "1");
}

function is_tablet(): bool {
    $result = false;
    
    if (class_exists("Mobile_Detect")) {
        $detect = new Mobile_Detect();
        $result = $detect->isTablet();
    }
    return $result;
}

function is_admin(): bool {
    $isAdmin = false;
    $user_id = get_user_id();
    if ($user_id) {
        $user = new User(get_user_id());
        $isAdmin = $user->getAdmin();
    }
    return $isAdmin;
}

// Check if it is night (current hour between 0 and 4 o'Clock AM)
function is_night(?int $time = null): bool {
    $time = $time ? $time : time();
    $hour = (int) date("G", $time);
    return ($hour >= 0 and $hour <= 4);
}

function is_debug_mode(): bool {
    $config = new CMSConfig();
    return (defined("ULICMS_DEBUG") and ULICMS_DEBUG)
            or ( isset($config->debug) and $config->debug);
}

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
        return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)),
                        $needle) === 0);
    }
    return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)),
                    $needle) === 0);
}

function var_is_type($var, $type, $required = false): bool {
    $methodName = "is_{$type}";

    if ($var === null or $var === "") {
        return !$required;
    }

    if (function_exists($methodName)) {
        return $methodName($var);
    }
    return false;
}

// returns true if $needle is a substring of $haystack
function str_contains($needle, $haystack): bool {
    return strpos($haystack, $needle) !== false;
}
