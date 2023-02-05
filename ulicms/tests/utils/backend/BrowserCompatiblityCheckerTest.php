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

    public function testIsSafari() {
        $checker = new BrowserCompatiblityChecker(
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) "
                . "AppleWebKit/537.36 (KHTML, like Gecko) "
                . "Chrome/81.0.4044.141 Safari/537.36"
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

    public function testLynx() {
        $checker = new BrowserCompatiblityChecker(
                "Lynx/2.8.9dev.16 libwww-FM/2.14 SSL-MM/1.4.1 GNUTLS/3.5.17"
        );
        $this->assertFalse($checker->isCompatible());
        $this->assertEquals(
                "Lynx",
                $checker->getUnsupportedBrowserName()
        );
    }

    public function testElinks() {
        $checker = new BrowserCompatiblityChecker(
                "ELinks/0.12pre6 (textmode; Linux 4.15.0-13-generic x86_64; 204x55-2)"
        );
        $this->assertFalse($checker->isCompatible());
        $this->assertEquals(
                "ELinks",
                $checker->getUnsupportedBrowserName()
        );
    }

    public function testLinks() {
        $checker = new BrowserCompatiblityChecker(
                "Links (2.1pre11; Linux 2.4.22 i686; fb)"
        );
        $this->assertFalse($checker->isCompatible());
        $this->assertEquals(
                "Links",
                $checker->getUnsupportedBrowserName()
        );
    }

    public function testW3m() {
        $checker = new BrowserCompatiblityChecker(
                "w3m/0.5.3+git20180125"
        );
        $this->assertFalse($checker->isCompatible());
        $this->assertEquals(
                "w3m",
                $checker->getUnsupportedBrowserName()
        );
    }

    public function testDillo() {
        $checker = new BrowserCompatiblityChecker(
                "Dillo/3.0.5	"
        );
        $this->assertFalse($checker->isCompatible());
        $this->assertEquals(
                "Dillo",
                $checker->getUnsupportedBrowserName()
        );
    }

    public function testOpera() {
        $checker = new BrowserCompatiblityChecker(
                "Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.18"
        );
        $this->assertTrue($checker->isCompatible());
        $this->assertNull($checker->getUnsupportedBrowserName());
    }

    public function testOperaMini() {
        $checker = new BrowserCompatiblityChecker(
                "Opera/9.80 (Android; Opera Mini/12.0.1987/37.7327; U; pl) "
                . "Presto/2.12.423 Version/12.16	"
        );
        $this->assertFalse($checker->isCompatible());
        $this->assertEquals(
                "Opera Mini",
                $checker->getUnsupportedBrowserName()
        );
    }

}
