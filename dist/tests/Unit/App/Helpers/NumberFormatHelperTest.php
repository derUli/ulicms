<?php

use App\Helpers\NumberFormatHelper;

class NumberFormatHelperTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        require_once getLanguageFilePath('en');
    }

    public function testFormatSizeUnitsGB(): void {
        $this->assertEquals(
            '64.05 GB',
            NumberFormatHelper::formatSizeUnits(
                (float)(64 * 1024 * 1024 * 1024) + (55 * 1024 * 1024)
            )
        );
    }

    public function testFormatSizeUnitsMB(): void {
        $this->assertEquals(
            '64.05 MB',
            NumberFormatHelper::formatSizeUnits(
                (float)(64 * 1024 * 1024) + (55 * 1024)
            )
        );
    }

    public function testFormatSizeUnitsKB(): void {
        $this->assertEquals(
            '64.05 KB',
            NumberFormatHelper::formatSizeUnits((float)(64 * 1024) + 55)
        );
    }

    public function testFormatSizeUnitsBytes(): void {
        $this->assertEquals(
            '64 B',
            NumberFormatHelper::formatSizeUnits((float)64)
        );
        $this->assertEquals(
            '0 B',
            NumberFormatHelper::formatSizeUnits((float)0)
        );
    }

    public function testFormatSizeUnitsByte(): void {
        $this->assertEquals(
            '1 B',
            NumberFormatHelper::formatSizeUnits((float)1)
        );
    }

    public function testFormatTime(): void {
        $number = time() - (60 * 60 * 24 * 367 * 2);
        $this->assertEquals(
            'vor über 2 Jahren',
            NumberFormatHelper::formatTime($number)
        );
    }

    public function testTimestampToSqlDateWithoutArgs(): void {
        $this->assertMatchesRegularExpression(
            '/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})$/',
            NumberFormatHelper::timestampToSqlDate()
        );
    }

    public function testTimestampToSqlDateWithTimestamp(): void {
        $timestamp = 1568118319;
        $this->assertEquals(
            '2019-09-10 14:25',
            NumberFormatHelper::timestampToSqlDate($timestamp)
        );
    }

    public function testTimestampToSqlDateWithTimestampAndFormat(): void {
        $timestamp = 1568118319;
        $this->assertEquals(
            '2019-09-10 14:25:19',
            NumberFormatHelper::timestampToSqlDate(
                $timestamp,
                NumberFormatHelper::SQL_DATE_WITH_SECONDS
            )
        );
    }
}
