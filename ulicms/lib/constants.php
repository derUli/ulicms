<?php

declare(strict_types=1);

/**
 * Defines a constant if it isn't defined yet
 * @param string $key Constant Name
 * @param type $value Value
 * @return bool returns false if the constant was already set
 */
function idefine(string $key, $value): bool {
    $key = strtoupper($key);
    if (!defined($key)) {
        define($key, $value);
        return true;
    }
    return false;
}
