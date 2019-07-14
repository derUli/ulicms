<?php

declare(strict_types=1);

namespace UliCMS\Helpers;

class DataTablesHelper extends \Helper {

    public static function getLanguageFileURL(string $lang): string {
        $baseUrl = "scripts/datatables/lang";
        $file = "{$baseUrl}/{$lang}.lang";
        if (file_exists($file)) {
            return $file;
        }
        return "$baseUrl/en.lang";
    }

}
