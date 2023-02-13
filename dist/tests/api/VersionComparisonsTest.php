<?php

use App\Utils\VersionComparison;

class VersionComparisonsTest extends \PHPUnit\Framework\TestCase
{
    public function testIsEqualReturnsTrue()
    {
        $this->assertTrue(VersionComparison::compare("1.0", "1.0.0", "="));
        $this->assertTrue(VersionComparison::compare("1", "1.0", "="));
        $this->assertTrue(VersionComparison::compare("1", "1", "="));
        $this->assertTrue(VersionComparison::compare("1.0.0", "1.0.0", "="));
        $this->assertTrue(VersionComparison::compare("2014.0.0", "2014", "="));
    }

    public function testIsEqualReturnsFalse()
    {
        $this->assertFalse(VersionComparison::compare("1.0", "1.1.1", "="));
        $this->assertFalse(VersionComparison::compare("1.0.0", "1.1", "="));
        $this->assertFalse(VersionComparison::compare("1", "2", "="));
        $this->assertFalse(VersionComparison::compare("2014.0.0", "2015", "="));
    }

    public function testIsGreaterReturnsTrue()
    {
        $this->assertTrue(VersionComparison::compare("1.1", "1.0", ">"));
        $this->assertTrue(VersionComparison::compare("2020.3.1", "2020.2", ">"));
        $this->assertTrue(VersionComparison::compare("2", "1.0.0", ">"));
    }

    public function testIsGreaterReturnsFalse()
    {
        $this->assertFalse(VersionComparison::compare("1", "1.0", ">"));
        $this->assertFalse(VersionComparison::compare("2020.1", "2020.2", ">"));
        $this->assertFalse(VersionComparison::compare("1.0.0", "1", ">"));
        $this->assertFalse(VersionComparison::compare("2.0.5c", "2.0.5c", ">"));
    }

    public function testIsLesserReturnsTrue()
    {
        $this->assertTrue(VersionComparison::compare("1.0", "1.1", "<"));
        $this->assertTrue(VersionComparison::compare("2020.2", "2020.3.1", "<"));
        $this->assertTrue(VersionComparison::compare("1.0.0", "2", "<"));
    }

    public function testIsLesserReturnsFalse()
    {
        $this->assertFalse(VersionComparison::compare("1.0", "1", "<"));
        $this->assertFalse(VersionComparison::compare("2020.2", "2020.1", "<"));
        $this->assertFalse(VersionComparison::compare("1", "1.0.0", "<"));
    }

    public function testIsGreaterOrEqualReturnsTrue()
    {
        $this->assertTrue(VersionComparison::compare("2020.2", "2020.2", ">="));
        $this->assertTrue(VersionComparison::compare("2020.2.0", "2020.2", ">="));
        $this->assertTrue(VersionComparison::compare("1.1", "1.0", ">="));
        $this->assertTrue(VersionComparison::compare("1.0.0", "1.0", ">="));
    }

    public function testIsGreaterOrEqualReturnsFalse()
    {
        $this->assertFalse(VersionComparison::compare("2020.1", "2020.2", ">="));
        $this->assertFalse(VersionComparison::compare("2020.2.0", "2020.3.1", ">="));
        $this->assertFalse(VersionComparison::compare("1.0.0", "1.1", ">="));
    }

    public function testIsLesserOrEqualReturnsTrue()
    {
        $this->assertTrue(VersionComparison::compare("2020.2", "2020.2", "<="));
        $this->assertTrue(VersionComparison::compare("1.0", "1.0.0", "<="));
        $this->assertTrue(VersionComparison::compare("1.2", "1.5", "<="));
        $this->assertTrue(VersionComparison::compare("1.7.3", "1.7.5", "<="));
    }

    public function testIsLesserOrEqualReturnsFalse()
    {
        $this->assertFalse(VersionComparison::compare("2020.2", "2020.1", "<="));
        $this->assertFalse(VersionComparison::compare("2021.1", "2020.3.1", "<="));
        $this->assertFalse(VersionComparison::compare("1.5", "1.2", "<="));
        $this->assertFalse(VersionComparison::compare("1.7.5", "1.7.2", "<="));
    }

    public function testNullValuesReturnsTrue()
    {
        $this->assertTrue(VersionComparison::compare(null, "2020.1", "<="));
        $this->assertFalse(VersionComparison::compare("2020.1", null, "<="));
        $this->assertTrue(VersionComparison::compare(null, null, ">="));
    }
}
