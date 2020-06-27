<?php

declare(strict_types=1);

// Get a subset of an associative array by providing the keys.
function array_keep(array $array, array $keys): array
{
    return array_intersect_key($array, array_fill_keys($keys, null));
}
