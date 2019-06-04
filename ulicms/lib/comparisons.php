<?php

function is_today($datetime = null) {
    $carbon = get_carbon($datetime);
    return $carbon->isToday();
}

function is_tomorrow($datetime = null) {
    $carbon = get_carbon($datetime);
    return $carbon->isTomorrow();
}

function is_yesterday($datetime = null) {
    $carbon = get_carbon($datetime);
    return $carbon->isYesterday();
}

function is_past($datetime = null) {
    $carbon = get_carbon($datetime);
    return $carbon->isPast();
}

function is_future($datetime = null) {
    $carbon = get_carbon($datetime);
    return $carbon->isPast();
}

function get_carbon($datetime = null) {
    return new Carbon($datetime ?? time(), date_default_timezone_get);
}

function is_blank($val = null) {
    return isset($val) && (is_string($val) &&
            StringHelper::isNullOrWhitespace($val)) ||
            empty($val);
}

function is_present($val = null) {
    return isset($val) && !is_blank($val);
}

function is_json($str) {
    return json_decode($str) != null;
}

function is_numeric_array($var) {
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

function is_true($var) {
    return (isset($var) and $var);
}

function is_false($var) {
    return !(isset($var) and $var);
}

// sind wir gerade im Adminordner?
function is_admin_dir() {
    return basename(getcwd()) === "admin";
}

function is_desktop() {
    return !is_mobile();
}

function is_crawler($useragent = null) {
    if (is_null($useragent)) {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
    }
    $isCrawler = apply_filter($useragent, "is_crawler");
    if (is_bool($isCrawler) or is_int($isCrawler)) {
        return boolval($isCrawler);
    }

    $crawlers = 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|' . 'AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|' . 'GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby';
    $isCrawler = (preg_match("/$crawlers/", $useragent) > 0);
    return $isCrawler;
}

// 21. Februar 2015
// Nutzt nun die Klasse Mobile_Detect
function is_mobile() {
    if (!class_exists("Mobile_Detect")) {
        return false;
    }
    $detect = new Mobile_Detect();
    $result = $detect->isMobile();
    if (Settings::get("no_mobile_design_on_tablet") and $result and $detect->isTablet()) {
        $result = false;
    }
    if (function_exists("apply_filter")) {
        $result = apply_filter($result, "is_mobile");
    }
    return $result;
}

function isMaintenanceMode() {
    return (strtolower(Settings::get("maintenance_mode")) == "on" || strtolower(Settings::get("maintenance_mode")) == "true" || Settings::get("maintenance_mode") == "1");
}

function is_tablet() {
    if (!class_exists("Mobile_Detect")) {
        return false;
    }
    $detect = new Mobile_Detect();
    $result = $detect->isTablet();
    return $result;
}

function is_admin() {
    $isAdmin = false;
    $user_id = get_user_id();
    if ($user_id) {
        $user = new User(get_user_id());
        $isAdmin = $user->getAdmin();
    }
    return $isAdmin;
}

// Check if it is night (current hour between 0 and 4 o'Clock AM)
function is_night() {
    $hour = (int) date("G", time());
    return ($hour >= 0 and $hour <= 4);
}

function is_debug_mode() {
    $config = new CMSConfig();
    return (defined("ULICMS_DEBUG") and ULICMS_DEBUG) or ( isset($config->debug) and $config->debug);
}

function isCLI() {
    return php_sapi_name() == "cli";
}

function startsWith($haystack, $needle, $case = true) {
    if ($case) {
        return (strcmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
    }
    return (strcasecmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
}

function endsWith($haystack, $needle, $case = true) {
    if ($case) {
        return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0);
    }
    return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0);
}
