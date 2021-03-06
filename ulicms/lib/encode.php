<?php

declare(strict_types=1);

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

// like json_encode() but human readable
function json_readable_encode($in, $indent = 0): string
{
    $_myself = __FUNCTION__;
    $_escape = function ($str) {
        return preg_replace("!([\b\t\n\r\f\"\\'])!", "\\\\\\1", $str);
    };

    $out = '';

    foreach ($in as $key => $value) {
        $out .= str_repeat("\t", $indent + 1);
        $out .= "\"" . $_escape((string) $key) . "\": ";

        if (is_object($value) || is_array($value)) {
            $out .= "\n";
            $out .= $_myself($value, $indent + 1);
        } elseif (is_bool($value)) {
            $out .= $value ? 'true' : 'false';
        } elseif (is_null($value)) {
            $out .= 'null';
        } elseif (is_string($value)) {
            $out .= "\"" . $_escape($value) . "\"";
        } else {
            $out .= $value;
        }

        $out .= ",\n";
    }

    if (!empty($out)) {
        $out = substr($out, 0, - 2);
    }

    $out = str_repeat("\t", $indent) . "{\n" . $out;
    $out .= "\n" . str_repeat("\t", $indent) . "}";

    return $out;
}
