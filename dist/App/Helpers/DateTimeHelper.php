<?php

declare(strict_types=1);

namespace App\Helpers;

use DateTimeZone;

/**
 * This class contains methods to deal with DateTimes
 */
class DateTimeHelper
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
        return setlocale(LC_ALL, 0) ?? null;
    }
}