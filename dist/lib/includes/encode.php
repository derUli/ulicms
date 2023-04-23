<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

use Nette\Utils\Json;

/**
 * Like var_dump() but returns it as string
 *
 * @return string
 */
function var_dump_str(): string {
    $result = '';
    $argc = func_num_args();
    $argv = func_get_args();

    if ($argc > 0) {
        ob_start();
        call_user_func_array('var_dump', $argv);
        $result = (string)ob_get_contents();
        ob_end_clean();

    }

    return $result;
}

/**
 * Like json_encode() but human readable
 *
 * @param mixed $value
 *
 * @return string
 */
function json_readable_encode(mixed $value): string {
    return Json::encode($value, true, false, false, true);
}
