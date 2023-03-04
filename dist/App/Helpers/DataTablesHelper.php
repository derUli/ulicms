<?php

declare(strict_types=1);

namespace App\Helpers;

defined('ULICMS_ROOT') or exit('no direct script access allowed');

use Helper;

/**
 * This tool returns methods to use DataTables (https://datatables.net/)
 */
class DataTablesHelper extends Helper
{
    /**
     * Returns a path to DataTables lang file based on $lang
     * If there is no translation for the given language returns path
     * to english translation
     * @param string $lang Language Code
     * @return string Path to language file
     */
    public static function getLanguageFileURL(string $lang): string
    {
        $baseUrl = "scripts/datatables/lang";
        $file = "{$baseUrl}/{$lang}.lang";
        if (is_file($file)) {
            return $file;
        }
        return "$baseUrl/en.lang";
    }
}
