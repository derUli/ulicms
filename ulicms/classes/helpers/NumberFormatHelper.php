<?php

namespace UliCMS\Helpers;

use Helper;

class NumberFormatHelper extends Helper {

    // Snippet from PHP Share: http://www.phpshare.org
    public static function formatSizeUnits(int $bytes) {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' Bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' Byte';
        } else {
            $bytes = '0 Bytes';
        }

        return $bytes;
    }

    public static function timestampToHtml5Datetime(?int $timestamp = null): string {
        $time = !is_null($timestamp) ? $timestamp : time();
        return date("Y-m-d\TH:i", $time);
    }

    public static function formatTime($seconds) {
        $seconds = abs($seconds); // Ganzzahlwert bilden
        return sprintf(get_translation("FORMAT_TIME"), $seconds / 60 / 60 / 24, ($seconds / 60 / 60) % 24, ($seconds / 60) % 60, $seconds % 60);
    }

}
