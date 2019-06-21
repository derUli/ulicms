<?php

use Carbon\Carbon;

class DateComparisonsTest extends \PHPUnit\Framework\TestCase {

    public function testIsTodayReturnsTrue() {
        $this->assertTrue(is_today(date('c')));
        $this->assertTrue(is_today(time()));
    }

    public function testIsTodayReturnsFalse() {
        $this->assertFalse(is_today("2019-02-27"));
        $this->assertFalse(is_today(time() - (60 * 60 * 24 * 2)));
    }

    public function testIsYesterdayReturnsTrue() {
        $yesterday = time() - (60 * 60 * 24 );
        $this->assertTrue(is_yesterday(date("c", $yesterday)));
        $this->assertTrue(is_yesterday($yesterday));
    }

    public function testIsYesterdayReturnsFalse() {
        $this->assertFalse(is_yesterday("2025-07-27"));
        $this->assertFalse(is_today(time() + (60 * 60 * 24 * 2)));
    }

    public function testIsTomorrowReturnsTrue() {
        $tomorrow = time() + (60 * 60 * 24);
        $this->assertTrue(is_tomorrow(date("c", $tomorrow)));
        $this->assertTrue(is_tomorrow($tomorrow));
    }

    public function testIsTomorrowReturnsFalse() {
        $yesterday = time() - (60 * 60 * 24);
        $this->assertFalse(is_tomorrow(date("c", $yesterday)));
        $this->assertFalse(is_tomorrow($yesterday));
    }

    public function testIsPastReturnsTrue() {
        $this->assertTrue(is_past(time() - 5));
    }

    public function testIsPastReturnsFalse() {
        $this->assertFalse(is_past(time() + 5));
    }

    public function testIsFutureReturnsTrue() {
        $this->assertTrue(is_future(time() + 5));
    }

    public function testIsFutureReturnsFalse() {
        $this->assertFalse(is_future(time() - 5));
    }

    public function testGetCarbon() {
        $this->assertInstanceOf(Carbon::class, get_carbon("2019-04-14"));
    }

}
