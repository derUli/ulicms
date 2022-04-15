<?php

declare(strict_types=1);

namespace UliCMS\Helpers;

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use Helper;
use Westsworld\TimeAgo;
use DateTime;

/**
 * Contains util methos to format number values
 */
class NumberFormatHelper extends Helper {

    const SQL_DATE_WITH_SECONDS = "Y-m-d H:i:s";
    const SQL_DATE_WITHOUT_SECONDS = "Y-m-d H:i";
    const SQL_DATE_WITH_TIME = "Y-m-d";

    /**
     * This method formats bytes in a human readable format
     * Snippet from PHP Share: http://www.phpshare.org
     * @param float $bytes Bytes
     * @return string Formatted File Size
     */
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

    /**
     * Converts a Unix timestamp to MySQL 
     * @param int|null $timestamp Unix Timestamp or null for current timestamp
     * @param string $format format string
     * @return string Formatted SQL Date
     */
    public static function timestampToSqlDate(
            ?int $timestamp = null,
            string $format = self::SQL_DATE_WITHOUT_SECONDS
    ): string {
        $time = !is_null($timestamp) ? $timestamp : time();
        return date($format, $time);
    }

    // Use this to format the time at "Online since"
    public static function formatTime(int $time): string {
        $dateTime = new DateTime();
        $dateTime->setTimestamp($time);

        $languageClass = "\\Westsworld\\TimeAgo\\Translations\\" .
                ucfirst(getSystemLanguage());
        $language = class_exists($languageClass) ? new $languageClass() : new \Westsworld\TimeAgo\Translations\De();

        $timeAgo = new TimeAgo($language);
        return $timeAgo->inWords($dateTime);
    }

}
