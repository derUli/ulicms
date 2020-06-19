<?php

use function UliCMS\Utils\VersionComparison\isEqual;
use function UliCMS\Utils\VersionComparison\isGreater;
use function UliCMS\Utils\VersionComparison\isLesser;

class VersionComparisonsTest extends \PHPUnit\Framework\TestCase {

    public function testIsEqualReturnsTrue() {
        $this->assertTrue(isEqual("1.0", "1.0.0"));
        $this->assertTrue(isEqual("1", "1.0"));
        $this->assertTrue(isEqual("1", "1"));
        $this->assertTrue(isEqual("1.0.0", "1.0.0"));
        $this->assertTrue(isEqual("2014.0.0", "2014"));
    }

    public function testIsEqualReturnsFalse() {
        $this->assertFalse(isEqual("1.0", "1.1.1"));
        $this->assertFalse(isEqual("1.0.0", "1.1"));
        $this->assertFalse(isEqual("1", "2"));
        $this->assertFalse(isEqual("2014.0.0", "2015"));
    }

    public function testIsGreaterReturnsTrue() {
        $this->assertTrue(isGreater("1.1", "1.0"));
        $this->assertTrue(isGreater("2020.2.3", "2020.2"));
        $this->assertTrue(isGreater("2", "1.0.0"));
    }

    public function testIsGreaterReturnsFalse() {
        $this->assertFalse(isGreater("1", "1.0"));
        $this->assertFalse(isGreater("2020.1", "2020.2"));
        $this->assertFalse(isGreater("1.0.0", "1"));
    }
    public function testIsLesserReturnsTrue() {
        $this->assertTrue(isLesser("1.0", "1.1"));
        $this->assertTrue(isLesser("2020.2", "2020.2.3",));
        $this->assertTrue(isLesser("1.0.0", "2"));
    }

    public function testIsLesserReturnsFalse() {
        $this->assertFalse(isLesser("1.0", "1"));
        $this->assertFalse(isLesser("2020.2", "2020.1"));
        $this->assertFalse(isLesser("1", "1.0.0"));
    }

}
