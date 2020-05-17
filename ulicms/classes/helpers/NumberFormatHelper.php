<?php

declare(strict_types=1);

namespace UliCMS\Helpers;

use Helper;
use Westsworld\TimeAgo;

class NumberFormatHelper extends Helper {

    // This method formats bytes in a human readable format
    // Snippet from PHP Share: http://www.phpshare.org
    public static function formatSizeUnits(float $bytes): string {
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

        // use this to convert an integer timestamp to use it
    // for a html5 datetime-local input
    public static function timestampToSqlDate(
            ?int $timestamp = null
    ): string {
        $time = !is_null($timestamp) ? $timestamp : time();
        return date("Y-m-d H:i", $time);
    }
    
    // use this to convert an integer timestamp to use it
    // for a html5 datetime-local input
    public static function timestampToHtml5Datetime(
            ?int $timestamp = null
    ): string {
        $time = !is_null($timestamp) ? $timestamp : time();
        return date("Y-m-d\TH:i", $time);
    }

    // Use this to format the time at "Online since"
    public static function formatTime(int $time): string {
        $dateTime = new DateTime();
        $dateTime->setTimestamp($time);


        $languageClass = "\\Westsworld\\TimeAgo\\Translations\\" .
                ucfirst(getSystemLanguage());
        $language = class_exists($languageClass) ? new $languageClass() :
                new \Westsworld\TimeAgo\Translations\De();

        $timeAgo = new TimeAgo($language);
        return $timeAgo->inWords($dateTime);
    }

}
