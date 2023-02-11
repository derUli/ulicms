<?php

use function App\Utils\VersionComparison\isEqual;
use function App\Utils\VersionComparison\isGreater;
use function App\Utils\VersionComparison\isLesser;
use function App\Utils\VersionComparison\isGreaterOrEqual;
use function App\Utils\VersionComparison\isLesserOrEqual;

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
        $this->assertTrue(isGreater("2020.3.1", "2020.2"));
        $this->assertTrue(isGreater("2", "1.0.0"));
    }

    public function testIsGreaterReturnsFalse() {
        $this->assertFalse(isGreater("1", "1.0"));
        $this->assertFalse(isGreater("2020.1", "2020.2"));
        $this->assertFalse(isGreater("1.0.0", "1"));
        $this->assertFalse(isGreater("2.0.5c", "2.0.5c"));
    }

    public function testIsLesserReturnsTrue() {
        $this->assertTrue(isLesser("1.0", "1.1"));
        $this->assertTrue(isLesser("2020.2", "2020.3.1",));
        $this->assertTrue(isLesser("1.0.0", "2"));
    }

    public function testIsLesserReturnsFalse() {
        $this->assertFalse(isLesser("1.0", "1"));
        $this->assertFalse(isLesser("2020.2", "2020.1"));
        $this->assertFalse(isLesser("1", "1.0.0"));
    }

    public function testIsGreaterOrEqualReturnsTrue() {
        $this->assertTrue(isGreaterOrEqual("2020.2", "2020.2"));
        $this->assertTrue(isGreaterOrEqual("2020.2.0", "2020.2"));
        $this->assertTrue(isGreaterOrEqual("1.1", "1.0"));
        $this->assertTrue(isGreaterOrEqual("1.0.0", "1.0"));
    }

    public function testIsGreaterOrEqualReturnsFalse() {
        $this->assertFalse(isGreaterOrEqual("2020.1", "2020.2"));
        $this->assertFalse(isGreaterOrEqual("2020.2.0", "2020.3.1"));
        $this->assertFalse(isGreaterOrEqual("1.0.0", "1.1"));
    }

    public function testIsLesserOrEqualReturnsTrue() {
        $this->assertTrue(isLesserOrEqual("2020.2", "2020.2"));
        $this->assertTrue(isLesserOrEqual("1.0", "1.0.0"));
        $this->assertTrue(isLesserOrEqual("1.2", "1.5"));
        $this->assertTrue(isLesserOrEqual("1.7.3", "1.7.5"));
    }

    public function testIsLesserOrEqualReturnsFalse() {
        $this->assertFalse(isLesserOrEqual("2020.2", "2020.1"));
        $this->assertFalse(isLesserOrEqual("2021.1", "2020.3.1"));
        $this->assertFalse(isLesserOrEqual("1.5", "1.2"));
        $this->assertFalse(isLesserOrEqual("1.7.5", "1.7.2"));
    }

    public function testNullValuesReturnsTrue() {
        $this->assertTrue(isLesserOrEqual(null, "2020.1"));
        $this->assertTrue(isGreater(null, null));
    }

}
