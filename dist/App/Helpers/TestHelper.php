<?php

declare(strict_types=1);

namespace App\Helpers;

defined('ULICMS_ROOT') or exit('no direct script access allowed');

use Closure;
use Exception;
use Helper;

/**
 * This class contains tools for running in context of unit tests
 */
class TestHelper extends Helper
{
    /**
     * Check if we are running unit tests
     * @return bool
     */
    public static function isRunningPHPUnit(): bool
    {
        return CORE_COMPONENT === CORE_COMPONENT_PHPUNIT;
    }

    /**
     * Executes a closure and captures it's output
     * @param Closure $method
     * @return string
     * @throws Exception
     */
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


    /**
     * Check if the application is running on a Windows machine
     * @return bool
     */
    public static function isWindowsServer(): bool
    {
        return defined("PHP_WINDOWS_VERSION_MAJOR");
    }
}