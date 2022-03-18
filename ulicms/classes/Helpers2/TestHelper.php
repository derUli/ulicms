<?php

declare(strict_types=1);

namespace UliCMS\Helpers;

use Closure;
use Exception;

class TestHelper extends \Helper {

    /**
     * Check if PHP is running in context of a Unit test
     * @return bool
     */
    public static function isRunningPHPUnit(): bool {
        return defined('PHPUNIT_COMPOSER_INSTALL') or defined('__PHPUNIT_PHAR__');
    }

    /**
     * Run Method capture output
     * @param Closure $method code to run
     * @return string Captured outputs
     * @throws Exception
     */
    public static function getOutput(Closure $method): string {
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
     * Check if the Server OS is windows
     * @return bool
     */
    public static function isWindowsServer(): bool {
        return defined("PHP_WINDOWS_VERSION_MAJOR");
    }

}
