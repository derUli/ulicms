<?php

namespace UliCMS\Helpers;

class DataTablesHelper extends \Helper {

    public static function getLanguageFileURL($lang) {
        $baseUrl = "scripts/datatables/lang";
        $file = "{$baseUrl}/{$lang}.lang";
        if (file_exists($file)) {
            return $file;
        }
        return "$baseUrl/en.lang";
    }

}
