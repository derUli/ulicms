<?php

declare(strict_types=1);

// replacement for the each() function which is deprecated since PHP 7.2.0
// used by kcfinder
function myEach(&$arr) {
    $key = key($arr);
    $result = ($key === null) ? false : [
        $key,
        current($arr),
        'key' => $key,
        'value' => current($arr)
    ];
    next($arr);
    return $result;
}

if (!function_exists("each")) {

    function each(&$arr) {
        return myEach($arr);
    }

}