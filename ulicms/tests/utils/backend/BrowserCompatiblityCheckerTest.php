<?php

use UliCMS\Backend\Utils\BrowserCompatiblityChecker;

class BrowserCompatiblityCheckerTest extends \PHPUnit\Framework\TestCase {

    public function testFirefox() {
        $checker = new BrowserCompatiblityChecker(
                "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:75.0) "
                . "Gecko/20100101 Firefox/75.0"
        );
        $this->assertTrue($checker->isCompatible());
        $this->assertNull($checker->getUnsupportedBrowserName());
    }

    public function testChrome() {
        $checker = new BrowserCompatiblityChecker(
                "Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
                . "AppleWebKit/537.36 (KHTML, like Gecko) "
                . "Chrome/81.0.4044.138 Safari/537.36"
        );
        $this->assertTrue($checker->isCompatible());
        $this->assertNull($checker->getUnsupportedBrowserName());
    }

    public function testIE() {
        $checker = new BrowserCompatiblityChecker(
                "Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; "
                . ".NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; "
                . ".NET CLR 3.5.30729; Zoom 3.6.0; rv:11.0) like Gecko"
        );
        $this->assertFalse($checker->isCompatible());
        $this->assertEquals(
                "Microsoft Internet Explorer",
                $checker->getUnsupportedBrowserName()
        );
    }

    public function testEdge() {
        $checker = new BrowserCompatiblityChecker(
                "Mozilla/5.0 (Windows NT 10.0; Win64; x64; ServiceUI 14) "
                . "AppleWebKit/537.36 (KHTML, like Gecko) "
                . "Chrome/70.0.3538.102 Safari/537.36 Edge/18.18362"
        );
        $this->assertTrue($checker->isCompatible());
        $this->assertNull($checker->getUnsupportedBrowserName());
    }

}
