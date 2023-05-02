<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

use App\Storages\Settings\MaintenanceMode;
use Detection\MobileDetect;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

/**
 * Checks if a variable is a decimal number
 *
 * @param mixed $val
 *
 * @return bool
 *
 */
function is_decimal(mixed $val): bool {
    return is_numeric($val) && ! ctype_digit((string)$val);
}

/**
 * Checks if a string is valid JSON
 *
 * @param string|null $str
 *
 * @return bool
 */
function is_json(?string $str): bool {
    try {
        Json::decode($str ?? '');
        return true;
    } catch (JsonException) {
        return false;
    }
}

/**
 * Checks if we are currently in admin dir
 *
 * @return bool
 */
function is_admin_dir(): bool {
    return basename(getcwd() ?: '') === 'admin';
}

/**
 * Checks by useragent if the client is a crawler
 *
 * @param string|null $useragent
 *
 * @return bool
 */
function is_crawler(?string $useragent = null): bool {
    $useragent = $useragent ?? Request::getUserAgent();

    $crawlerDetect = new CrawlerDetect();
    return $crawlerDetect->isCrawler($useragent);
}

/**
 * Checks by useragent if the current client is a mobile device
 *
 * @return bool
 */
function is_mobile(): bool {
    $mobileDetect = new MobileDetect();
    $result = $mobileDetect->isMobile();

    if ((bool)Settings::get('no_mobile_design_on_tablet') &&
            $result &&
            $mobileDetect->isTablet()) {
        $result = false;
    }

    return $result;
}

/**
 * Checks if the website is currently on maintenance mode
 *
 * @return bool
 */
function is_maintenance_mode(): bool {
    return MaintenanceMode::getInstance()->isEnabled();
}

/**
 * Checks by user agent if the current client is a tablet
 *
 * @return bool
 */
function is_tablet(): bool {
    $mobileDetect = new \Detection\MobileDetect();
    $result = $mobileDetect->isTablet();

    return $result;
}

/**
 * Checks if the script is run from command line
 *
 * @return bool
 */
function is_cli(): bool {
    return PHP_SAPI == 'cli';
}

/**
 * Checks if var has a type
 *
 * @param mixed $var
 * @param string $type
 * @param bool $required
 *
 * @return bool
 */
function var_is_type(mixed $var, string $type, bool $required = false): bool {
    $methodName = "is_{$type}";

    if ($var === null || $var === '') {
        return ! $required;
    }

    if (function_exists($methodName)) {
        return $methodName($var);
    }
    return false;
}

/**
 * Checks if $input is a valid version number
 *
 * @param string|null $input
 *
 * @return bool
 */
function is_version_number(?string $input): bool {
    return $input && version_compare($input, '0.0.1', '>=');
}
