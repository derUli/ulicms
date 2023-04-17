<?php

declare(strict_types=1);

namespace App\Helpers;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use ChrisUllyott\FileSize;
use DateTime;
use Westsworld\TimeAgo;

/**
 * Utils to deal with number values such as size units and timestamps
 */
abstract class NumberFormatHelper extends Helper
{
    public const SQL_DATE_WITH_SECONDS = 'Y-m-d H:i:s';

    public const SQL_DATE_WITHOUT_SECONDS = 'Y-m-d H:i';

    /**
     * Format filesizes in a more human readable format
     * @param float $bytes
     * @return string
     */
    public static function formatSizeUnits(float $bytes): string
    {
        $size = new FileSize("{$bytes} Bytes");
        return $size->asAuto();
    }

    /**
     * Converts Unix timestamps to html5 datetime-local input format
     * @param int|null $timestamp
     * @param string $format
     * @return string
     */
    public static function timestampToSqlDate(
        ?int $timestamp = null,
        string $format = self::SQL_DATE_WITHOUT_SECONDS
    ): string {
        $time = $timestamp ?? time();
        return date($format, $time);
    }

    /**
     * Human readable format of time differences
     * @param int $time
     * @return string
     */
    public static function formatTime(int $time): string
    {
        $dateTime = new DateTime();
        $dateTime->setTimestamp($time);

        // Translation class
        $translationClass = '\\Westsworld\\TimeAgo\\Translations\\';
        $translationClass .= ucfirst(getSystemLanguage());

        // If there is a translation class for the current language use it
        // else it will fallback to english as default
        $language = null;
        if (class_exists($translationClass)) {
            $language = new $translationClass();
        }

        // Format it as a human readable string
        //
        // "3 Hours ago"
        // "5 Months ago"
        // "2 Years ago"
        $timeAgo = new TimeAgo($language);
        return $timeAgo->inWords($dateTime);
    }
}
