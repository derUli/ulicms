<?php

declare(strict_types=1);

namespace App\Helpers;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use DateTimeZone;
use IntlDateFormatter;
use Settings;

/**
 * This class contains methods to deal with DateTimes
 */
abstract class DateTimeHelper
{
    /**
     * Get Current Timezone
     * @return \DateTimeZone
     */
    public static function getCurrentTimezone(): \DateTimeZone
    {
        return new DateTimeZone(date_default_timezone_get());
    }

    /**
     * Get name of current locale
     * @return string|null
     */
    public static function getCurrentLocale(): ?string
    {
        return Settings::getLang('locale');
    }

    /**
     * Format integer timestamp user readable
     * Format integer timestamp
     * @param int $timestamp
     * 
     * @return string
     */
    public static function timestampToFormattedDateTime(int $timestamp, int $dateType = IntlDateFormatter::MEDIUM, int $timeType = IntlDateFormatter::MEDIUM): ?string
    {
        $timezone = self::getCurrentTimezone();
        $currentLocale = self::getCurrentLocale();

        $formatter = new IntlDateFormatter($currentLocale, $dateType, $timeType, $timezone);
        $pattern = str_replace(',', '', $formatter->getPattern());
        $formatter->setPattern($pattern);

        return $formatter->format($timestamp) ? $formatter->format($timestamp) : null;
    }
}
