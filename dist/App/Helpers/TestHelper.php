<?php

declare(strict_types=1);

namespace App\Helpers;

defined('ULICMS_ROOT') or exit('no direct script access allowed');

use Closure;
use Exception;

class TestHelper extends \Helper
{
    // Check if PHP is running in context of a Unit test
    public static function isRunningPHPUnit(): bool
    {
        return defined('PHPUNIT_COMPOSER_INSTALL') ||
                defined('__PHPUNIT_PHAR__');
    }

    public static function getOutput(Closure $method): string
    {
        ob_start();
        try {
            $method();
            return ob_get_clean();
        } catch (Exception $e) {
            ob_get_clean();
            throw $e;
        }
    }

    public static function isWindowsServer(): bool
    {
        return defined("PHP_WINDOWS_VERSION_MAJOR");
    }
}
