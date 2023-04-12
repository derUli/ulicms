<?php

declare(strict_types=1);

use Nette\Utils\Json;

/**
 * Like var_dump() but returns it as string
 * @return string
 */
function var_dump_str(): string
{
    $argc = func_num_args();
    $argv = func_get_args();

    if ($argc > 0) {
        ob_start();
        call_user_func_array('var_dump', $argv);
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }

    return '';
}

/**
 * Like json_encode() but human readable
 * @param type $in
 * @return string
 */
function json_readable_encode($in): string
{
    return Json::encode($in, true, false, false, true);
}
