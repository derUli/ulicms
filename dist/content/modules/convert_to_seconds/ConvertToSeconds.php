<?php

declare(strict_types=1);

namespace content\modules\convert_to_seconds;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

class ConvertToSeconds {
    /**
     * Converts a timespan in a given unit to seconds
     *
     *
     * @param int $timespan
     * @param TimeUnit $unit
     * @return int $seconds
     */
    public static function convertToSeconds(int $timespan, TimeUnit $unit): int {
        return match ($unit) {
            TimeUnit::SECONDS => $timespan,
            TimeUnit::MINUTES => $timespan * 60,
            TimeUnit::HOURS => $timespan * 60 * 60,
            TimeUnit::DAYS => $timespan * 60 * 60 * 24,
            TimeUnit::MONTHS => $timespan * 60 * 60 * 24 * 30,
            TimeUnit::WEEKS => $timespan * 60 * 60 * 24 * 7,
            TimeUnit::YEARS => $timespan * 60 * 60 * 24 * 365,
            TimeUnit::DECADES => $timespan * 60 * 60 * 24 * 365 * 10
        };
    }
}
