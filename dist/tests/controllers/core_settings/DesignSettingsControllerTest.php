<?php

use App\Utils\File;

class DesignSettingsControllerTest extends \PHPUnit\Framework\TestCase
{
    private $initialSettings = [];

    protected function setUp(): void
    {
        $this->cleanUpFiles();

        $settings = [
            "mobile_theme",
            "theme"
        ];
        foreach ($settings as $setting) {
            $this->initialSettings[$setting] = Settings::get($setting);
        }
    }

    protected function tearDown(): void
    {
        $this->cleanUpFiles();

        foreach ($this->initialSettings as $key => $value) {
            if ($value === null) {
                Settings::delete($key);
            } else {
                Settings::set($key, $value);
            }
        }
    }

    private function cleanUpFiles()
    {
        $filesToDelete = [
            "ULICMS_GENERATED/design_variables.scss"
        ];
        foreach ($filesToDelete as $file) {
            $file = Path::resolve($file);
            File::deleteIfExists($file);
        }
    }

    public function test_generateSCSS()
    {
        $controller = ControllerRegistry::get(DesignSettingsController::class);
        $scss = $controller->_generateSCSS();
        $this->assertGreaterThanOrEqual(5, substr_count($scss, "\n"));
        $lines = explode("\n", trim(normalizeLN($scss, "\n")));

        $this->assertCount(12, $lines);
    }

    public function test_generateSCSSToFile()
    {
        $this->cleanUpFiles();
        $controller = ControllerRegistry::get(DesignSettingsController::class);
        $file = $controller->_generateSCSSToFile();
        $this->assertStringEndsWith(
            "/content/generated/design_variables.scss",
            $file
        );
        $this->assertFileExists($file);

        $fileContent = file_get_contents($file);

        $this->assertGreaterThanOrEqual(
            5,
            substr_count($fileContent, "\n")
        );
    }

    public function test_generateSCSSInConstructor()
    {
        $this->cleanUpFiles();

        $file = Path::resolve("ULICMS_GENERATED/design_variables.scss");
        $this->assertFileDoesNotExist($file);

        $controller = new DesignSettingsController();
        $this->assertFileExists($file);
        $this->assertEquals(
            $controller->_generateSCSS(),
            file_get_contents($file)
        );
    }

    public function testGetFontFamilys()
    {
        $controller = new DesignSettingsController();
        $fonts = $controller->getFontFamilys();
        $this->assertEquals(
            "Arial, 'Helvetica Neue', Helvetica, sans-serif",
            $fonts["Arial"]
        );
        $this->assertGreaterThanOrEqual(20, count($fonts));
        foreach ($fonts as $name => $family) {
            $this->assertNotEmpty($name);
            $this->assertNotEmpty($family);
        }
    }

    public function testGetThemePreviewReturnsPath()
    {
        $controller = new DesignSettingsController();
        $this->assertEquals(
            'content/templates/impro17/screenshot.jpg',
            $controller->_themePreview("impro17")
        );
    }

    public function testThemePreviewReturnsNull()
    {
        $controller = new DesignSettingsController();
        $this->assertNull($controller->_themePreview("nothing"));
    }

    public function testSetDefaultTheme()
    {
        $controller = new DesignSettingsController();
        $this->assertNotEquals("foobar", Settings::get('theme'));

        $controller->_setDefaultTheme("foobar");
        $this->assertEquals("foobar", Settings::get('theme'));
    }

    public function testSetDefaultMobileThemeWithTheme()
    {
        $controller = new DesignSettingsController();
        $this->assertNotEquals("foobar", Settings::get('theme'));

        $controller->_setDefaultMobileTheme("foobar");
        $this->assertEquals("foobar", Settings::get("mobile_theme"));
    }

    public function testSetDefaultMobileThemeWithNull()
    {
        $controller = new DesignSettingsController();
        $controller->_setDefaultMobileTheme("foobar");
        $controller->_setDefaultMobileTheme("foobar");
        $this->assertNull(Settings::get("mobile_theme"));
    }

    public function testGetFontSizes()
    {
        $controller = new DesignSettingsController();
        $sizes = $controller->getFontSizes();
        $this->assertCount(75, $sizes);
        $this->assertContains("14px", $sizes);
    }
}
