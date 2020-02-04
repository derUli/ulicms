<?php

declare(strict_types=1);

function idefine($key, $value): bool {
    $key = strtoupper($key);
    if (!defined($key)) {
        define($key, $value);
        return true;
    }
    return false;
}