<?php

declare(strict_types=1);

defined('ULICMS_ROOT') or exit('no direct script access allowed');

// is $val a decimal number or a integer?
function is_decimal($val): bool {
    return is_numeric($val) && !ctype_digit(strval($val));
}

function is_blank($val = null): bool {
    return isset($val) && (is_string($val) &&
            StringHelper::isNullOrWhitespace($val)) ||
            empty($val);
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
    return (isset($var) && $var);
}

function is_false($var): bool {
    return !is_true($var);
}

// sind wir gerade im Adminordner?
function is_admin_dir(): bool {
    return basename(getcwd()) === "admin";
}

function is_desktop(): bool {
    return !is_mobile();
}

function is_crawler(?string $useragent = null): bool {
    if (!$useragent && Request::getUserAgent()) {
        $useragent = Request::getUserAgent();
    }
    if (!$useragent) {
        return false;
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

    if (class_exists('\Detection\MobileDetect')) {
        $detect = new \Detection\MobileDetect();
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

    if (class_exists('\Detection\MobileDetect')) {
        $detect = new \Detection\MobileDetect();
        $result = $detect->isTablet();
    }
    return $result;
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

    if ($var === null or $var === "") {
        return !$required;
    }

    if (function_exists($methodName)) {
        return $methodName($var);
    }
    return false;
}

function is_version_number(?string $input): bool {
    return ($input and version_compare($input, '0.0.1', '>='));
}
