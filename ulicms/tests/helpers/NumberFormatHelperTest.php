<?php

class NumberFormatHelperTest extends \PHPUnit\Framework\TestCase
{

    public function setUp()
    {
        require_once getLanguageFilePath("en");
    }

    public function testFormatSizeUnitsGB()
    {
        $this->assertEquals("64.05 GB", NumberFormatHelper::formatSizeUnits(floatval((64 * 1024 * 1024 * 1024) + (55 * 1024 * 1024))));
    }

    public function testFormatSizeUnitsMB()
    {
        $this->assertEquals("64.05 MB", NumberFormatHelper::formatSizeUnits(floatval((64 * 1024 * 1024) + (55 * 1024))));
    }

    public function testFormatSizeUnitsKB()
    {
        $this->assertEquals("64.05 KB", NumberFormatHelper::formatSizeUnits(floatval((64 * 1024) + 55)));
    }

    public function testFormatSizeUnitsBytes()
    {
        $this->assertEquals("64 Bytes", NumberFormatHelper::formatSizeUnits(floatval(64)));
        $this->assertEquals("0 Bytes", NumberFormatHelper::formatSizeUnits(floatval(0)));
    }

    public function testFormatSizeUnitsByte()
    {
        $this->assertEquals("1 Byte", NumberFormatHelper::formatSizeUnits(floatval(1)));
    }

    public function testFormatTime()
    {
        $number = (60 * 60 * 24 * 365 * 3 + 123456);
        $this->assertEquals("1096 Days 10:17:36 Hours", NumberFormatHelper::formatTime($number));
    }
}