<?php

class DataTablesHelper
{

    public static function getLanguageFileURL($lang)
    {
        $baseUrl = "scripts/datatables/lang";
        $file = "{$baseUrl}/{$lang}.lang";
        if (file_exists($file)) {
            return $file;
        }
        return "$baseUrl/en.lang";
    }
}