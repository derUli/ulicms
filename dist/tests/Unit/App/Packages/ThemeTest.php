<?php

use App\Packages\Theme;

class ThemeTest extends \PHPUnit\Framework\TestCase {
    public function testGetVersionReturnsVersion(): void {
        $theme = new Theme('impro17');
        $this->assertEquals('2.1.6', $theme->getVersion());
    }

    public function testGetVersionReturnsNull(): void {
        $theme = new Theme('gibts_nicht');
        $this->assertNull($theme->getVersion());
    }

    public function testGetScreenshotReturnsFilename(): void {
        $theme = new Theme('impro17');
        $this->assertEquals('content/templates/impro17/screenshot.jpg', $theme->getScreenshotFile());
    }

    public function testGetScreenshotReturnsNull(): void {
        $theme = new Theme('gibts_nicht');
        $this->assertNull($theme->getScreenshotFile());
    }

    public function testHasScreenshotReturnsFilename(): void {
        $theme = new Theme('impro17');
        $this->assertTrue($theme->hasScreenshot());
    }

    public function testHasScreenshotReturnsNull(): void {
        $theme = new Theme('gibts_nicht');
        $this->assertFalse($theme->hasScreenshot());
    }
}
