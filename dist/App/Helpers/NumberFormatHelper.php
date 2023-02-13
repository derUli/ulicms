<?php

declare(strict_types=1);

namespace App\Helpers;

use Helper;
use Westsworld\TimeAgo;
use DateTime;
use ChrisUllyott\FileSize;

class NumberFormatHelper extends Helper
{
    public const SQL_DATE_WITH_SECONDS = "Y-m-d H:i:s";
    public const SQL_DATE_WITHOUT_SECONDS = "Y-m-d H:i";

    // This method formats bytes in a human readable format
    // Snippet from PHP Share: http://www.phpshare.org
    public static function formatSizeUnits(float $bytes): string
    {
        $size = new FileSize("$bytes Bytes");
        return $size->asAuto();
    }

    // use this to convert an integer timestamp to use it
    // for a html5 datetime-local input
    public static function timestampToSqlDate(
        ?int $timestamp = null,
        string $format = self::SQL_DATE_WITHOUT_SECONDS
    ): string {
        $time = $timestamp ?? time();
        return date($format, $time);
    }

    // Use this to format the time at "Online since"
    public static function formatTime(int $time): string
    {
        $dateTime = new DateTime();
        $dateTime->setTimestamp($time);

        $translationClass = '\\Westsworld\\TimeAgo\\Translations\\';
        $translationClass .= ucfirst(getSystemLanguage());

        $language = null;
        if (class_exists($translationClass)) {
            $language = new $translationClass();
        }

        $timeAgo = new TimeAgo($language);
        return $timeAgo->inWords($dateTime);
    }
}
