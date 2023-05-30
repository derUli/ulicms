<?php

declare(strict_types=1);

namespace content\modules\convert_to_seconds;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

/**
 * Use this constants for the $unit param of convertToSeconds()
 */
enum TimeUnit {
    case SECONDS;
    case MINUTES;
    case HOURS;
    case DAYS;
    case WEEKS;
    case MONTHS;
    case YEARS;
    case DECADES;
}
