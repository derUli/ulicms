<?php

class LegacyTest extends \PHPUnit\Framework\TestCase {

    public function testStrftimeSuccess() {
        $timestamp = 1643855645;

        $this->assertEquals('03.02.2022', PHP81_BC\strftime('%x', $timestamp));
        $this->assertEquals('2022', PHP81_BC\strftime('%Y', $timestamp));
        $this->assertEquals('02:34:05', PHP81_BC\strftime('%X', $timestamp));
        $this->assertEquals('20.22', PHP81_BC\strftime('%C', $timestamp));
        $this->assertEquals('4', PHP81_BC\strftime('%U', $timestamp));
        $this->assertEquals('4', PHP81_BC\strftime('%W', $timestamp));
        $this->assertEquals('3. Februar 2022 um 02:34', PHP81_BC\strftime('%c', $timestamp));
        $this->assertEquals('22', PHP81_BC\strftime('%g', $timestamp));
        $this->assertEquals('034', PHP81_BC\strftime('%j', $timestamp));
        $this->assertStringStartsWith("\n", PHP81_BC\strftime('%n', $timestamp));
        $this->assertEquals("\t", PHP81_BC\strftime('%t', $timestamp));
        $this->assertEquals("Donnerstag", PHP81_BC\strftime('%A', $timestamp));

        $this->assertEquals(4, strlen(PHP81_BC\strftime('%Y')));
        $this->assertEquals('2004', PHP81_BC\strftime('%Y', '2004-07-04'));
    }

    public function testStrftimeInvalidTimestamp() {
        $this->expectException(InvalidArgumentException::class);
        PHP81_BC\strftime('%x', new stdClass);
    }

    public function testStrftimeInvalidFormat() {
        $this->expectException(InvalidArgumentException::class);
        PHP81_BC\strftime('%q', time());
    }

}
