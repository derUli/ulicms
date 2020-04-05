<?php

namespace UliCMS\Helpers;

class TestHelper extends \Helper {

    // Check if PHP is running in context of a Unit test
    public static function isRunningPHPUnit(): bool {
        return defined('PHPUNIT_COMPOSER_INSTALL') or defined('__PHPUNIT_PHAR__');
    }

}
