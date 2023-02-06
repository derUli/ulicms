<?php

use App\Packages\Theme;

class ThemeTest extends \PHPUnit\Framework\TestCase {

    public function testGetVersionReturnsVersion() {
        $theme = new Theme("impro17");
        $this->assertEquals("2.1.5", $theme->getVersion());
    }

    public function testGetVersionReturnsNull() {
        $theme = new Theme("gibts_nicht");
        $this->assertNull($theme->getVersion());
    }

    public function testGetScreenshotReturnsFilename() {
        $theme = new Theme("impro17");
        $this->assertEquals("content/templates/impro17/screenshot.jpg", $theme->getScreenshotFile());
    }

    public function testGetScreenshotReturnsNull() {
        $theme = new Theme("gibts_nicht");
        $this->assertNull($theme->getScreenshotFile());
    }

    public function testHasScreenshotReturnsFilename() {
        $theme = new Theme("impro17");
        $this->assertTrue($theme->hasScreenshot());
    }

    public function testHasScreenshotReturnsNull() {
        $theme = new Theme("gibts_nicht");
        $this->assertFalse($theme->hasScreenshot());
    }

}
