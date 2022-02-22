<?php

declare(strict_types=1);

function idefine(string $key, $value): bool
{
    $key = strtoupper($key);
    if (!defined($key)) {
        define($key, $value);
        return true;
    }
    return false;
}
