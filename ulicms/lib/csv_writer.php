<?php

declare(strict_types=1);

/**
 * outputCSV creates a line of CSV and outputs it to browser
 */
function outputCSV(array $array): void
{
    $fp = fopen('php://output', 'w'); // this file actual writes to php output
    fputcsv($fp, $array);
    fclose($fp);
}

/**
 * getCSV creates a line of CSV and returns it.
 */
function getCSV(array $array): string
{
    ob_start(); // buffer the output ...
    outputCSV($array);
    return trim(ob_get_clean()); // ... then return it as a string!
}
