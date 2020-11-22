<?php

class FaviconControllerTest extends \PHPUnit\Framework\TestCase {

    public function testGetSizes() {
        $controller = new FaviconController();
        $sizes = $controller->_getSizes();
        $this->assertCount(2, $sizes);
        $this->assertEquals([32, 32], $sizes[0]);
        $this->assertEquals([64, 64], $sizes[1]);
    }

    public function testGetSizesWithHighResolution() {
        $controller = new FaviconController();
        $sizes = $controller->_getSizes(true);
        $this->assertCount(3, $sizes);
        $this->assertEquals([32, 32], $sizes[0]);
        $this->assertEquals([64, 64], $sizes[1]);
        $this->assertEquals([128, 128], $sizes[2]);
    }

    public function testGetDestination1() {
        $controller = new FaviconController();

        $this->assertStringEndsWith(
                "/content/images/favicon.ico",
                $controller->_getDestination1()
        );
    }

    public function testGetDestination2() {
        $controller = new FaviconController();

        $this->assertStringEndsWith(
                "/favicon.ico",
                $controller->_getDestination2()
        );
        $this->assertStringEndsNotWith(
                "/content/images/favicon.ico",
                $controller->_getDestination2()
        );
    }

}
