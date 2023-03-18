<?php

use App\Helpers\DateTimeHelper;

class DateTimeHelperTest extends \PHPUnit\Framework\TestCase
{
    public function testGetCurrentTimezone()
    {
        $timezone = DateTimeHelper::getCurrentTimezone();
        $this->assertInstanceOf(DateTimeZone::class, $timezone);
        $this->assertEquals('Europe/Berlin', $timezone->getName());
    }

    public function testGetCurrentLocale()
    {
        $locale = DateTimeHelper::getCurrentLocale();
        $this->assertIsString($locale);
        $this->assertNotEmpty($locale);
    }
}
