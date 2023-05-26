<?php

use App\Constants\DateTime;
use App\Helpers\DateTimeHelper;

class DateTimeHelperTest extends \PHPUnit\Framework\TestCase {
    public function testGetCurrentTimezone(): void {
        $timezone = DateTimeHelper::getCurrentTimezone();
        $this->assertInstanceOf(DateTimeZone::class, $timezone);
        $this->assertEquals('Europe/Berlin', $timezone->getName());
    }

    public function testGetCurrentLocale(): void {
        $locale = DateTimeHelper::getCurrentLocale();
        $this->assertIsString($locale);
        $this->assertNotEmpty($locale);
    }

    public function testTimestampToFormattedDateTime(): void {
        $fourtyYears = DateTime::ONE_DAY_IN_SECONDS * 365 * 40;
        $thirtyTwoMinutes = 60 * 32;
        $elevenSeconds = 11;
        $datetimeString = DateTimeHelper::timestampToFormattedDateTime($fourtyYears + $thirtyTwoMinutes + $elevenSeconds);

        $this->assertStringStartsWith('22.12.2009', $datetimeString);
        $this->assertStringEndsWith(':32:11', $datetimeString);
    }

    public function testIsValidTimezoneReturnsTrue(): void {
        $this->assertTrue(DateTimeHelper::isValidTimezone('Europe/London'));
    }

    public function testIsValidTimezoneReturnsFalse(): void {
        $this->assertFalse(DateTimeHelper::isValidTimezone('Asia/Lampukistan'));
    }
}
