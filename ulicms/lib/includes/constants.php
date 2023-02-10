<?php

declare(strict_types=1);

defined('ULICMS_ROOT') or exit('no direct script access allowed');

/**
 * Defines a constant if not yet defined.
 * @param string $name Name of constant, will be converted to uppercase
 * @param type $value Value for constant
 * @return bool
 */
function idefine(string $name, $value): bool {
    $success = false;

    $name = strtoupper($name);

    if (!defined($name)) {
        define($name, $value);
        $success = true;
    }

    return $success;
}
