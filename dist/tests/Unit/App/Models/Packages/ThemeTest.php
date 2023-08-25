<?php

use App\Models\Packages\Theme;

class ThemeTest extends \PHPUnit\Framework\TestCase {
    public function testGetVersionReturnsVersion(): void {
        $theme = new Theme('impro17');
        $this->assertEquals('2.1.8', $theme->getVersion());
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

    public function testIsInstalledReturnsTrue(): void {
        $theme = new Theme('impro17');
        $this->assertTrue($theme->isInstalled());
    }

    public function testIsInstalledReturnsFalse1(): void {
        $theme = new Theme('not_found');
        $this->assertFalse($theme->isInstalled());
    }

    public function testIsInstalledReturnsFalse2(): void {
        $theme = new Theme('.');
        $this->assertFalse($theme->isInstalled());
    }

    public function testIsInstalledReturnsFalse3(): void {
        $theme = new Theme('..');
        $this->assertFalse($theme->isInstalled());
    }
}
