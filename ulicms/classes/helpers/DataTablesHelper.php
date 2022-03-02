<?php

declare(strict_types=1);

namespace UliCMS\Helpers;

/**
 * Methods for DataTables
 */
class DataTablesHelper extends \Helper {

    /**
     * Returns the path of the datatables language file
     * if there is no language file for the current language
     * it returns the path to the english language file
     * @param string $lang Language Code
     * @return string Language File URL 
     */
    public static function getLanguageFileURL(string $lang): string {
        $baseUrl = "scripts/datatables/lang";
        $file = "{$baseUrl}/{$lang}.lang";
        if (file_exists($file)) {
            return $file;
        }
        return "$baseUrl/en.lang";
    }

}
