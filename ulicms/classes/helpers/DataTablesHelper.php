<?php

declare(strict_types=1);

namespace UliCMS\Helpers;

class DataTablesHelper extends \Helper {

    // returns the path of the datatables language file
    // if there is no language file for the current language
    // it returns the path to the english language file
    public static function getLanguageFileURL(string $lang): string {
        $baseUrl = "scripts/datatables/lang";
        $file = "{$baseUrl}/{$lang}.lang";
        if (file_exists($file)) {
            return $file;
        }
        return "$baseUrl/en.lang";
    }

}
