<?php

use UliCMS\Utils\File;

class DesignSettingsControllerTest extends \PHPUnit\Framework\TestCase {

    private $initialSettings = [];

    public function setUp() {
        $this->cleanUpFiles();

        $settings = [
            "mobile_theme",
            "theme"
        ];
        foreach ($settings as $setting) {
            $this->initialSettings[$setting] = Settings::get($setting);
        }
    }

    public function tearDown() {
        $this->cleanUpFiles();
        Settings::delete("disable_custom_layout_options");

        foreach ($this->initialSettings as $key => $value) {
            if ($value === null) {
                Settings::delete($key);
            } else {
                Settings::set($key, $value);
            }
        }
    }

    private function cleanUpFiles() {
        $filesToDelete = [
            "ULICMS_GENERATED/design_variables.scss"
        ];
        foreach ($filesToDelete as $file) {
            $file = Path::resolve($file);
            File::deleteIfExists($file);
        }
    }

    public function testGenerateSCSS() {
        $controller = ControllerRegistry::get(DesignSettingsController::class);
        $scss = $controller->GenerateSCSS();
        $this->assertGreaterThanOrEqual(5, substr_count($scss, "\n"));
        $lines = explode("\n", trim(normalizeLN($scss, "\n")));

        $this->assertCount(12, $lines);
    }

    public function testGenerateSCSSReturnsNull() {
        Settings::set("disable_custom_layout_options", "1");
        $controller = ControllerRegistry::get(DesignSettingsController::class);
        $scss = $controller->GenerateSCSS();
        $this->assertNull($scss);
    }

    public function testGenerateSCSSToFileReturnsNull() {
        Settings::set("disable_custom_layout_options", "1");
        $controller = ControllerRegistry::get(DesignSettingsController::class);
        $scss = $controller->generateSCSSToFile();
        $this->assertNull($scss);
    }

    function testGenerateSCSSToFile() {
        $this->cleanUpFiles();
        $controller = ControllerRegistry::get(DesignSettingsController::class);
        $file = $controller->generateSCSSToFile();
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

    public function testGenerateSCSSInConstructor() {
        $this->cleanUpFiles();

        $file = Path::resolve("ULICMS_GENERATED/design_variables.scss");
        $this->assertFileNotExists($file);

        $controller = new DesignSettingsController();
        $this->assertFileExists($file);
        $this->assertEquals($controller->generateSCSS(),
                file_get_contents($file));
    }

    public function testGetFontFamilys() {
        $controller = new DesignSettingsController();
        $fonts = $controller->getFontFamilys();
        $this->assertEquals(
                "Arial, 'Helvetica Neue', Helvetica, sans-serif",
                $fonts["Arial"]
        );
        $this->assertGreaterThanOrEqual(21, count($fonts));
        foreach ($fonts as $name => $family) {
            $this->assertNotEmpty($name);
            $this->assertNotEmpty($family);
        }
    }

    public function testGetGoogleFonts() {
        $controller = new DesignSettingsController();
        $fonts = $controller->getGoogleFonts();
        $this->assertCount(732, $fonts);
        foreach ($fonts as $font) {
            $this->assertIsString($font);
            $this->assertNotEmpty($font);
            $this->assertGreaterThanOrEqual(3, strlen($font));
        }

        $this->assertContains("Roboto", $fonts);
        $this->assertContains("Open Sans", $fonts);
        $this->assertContains("Lato", $fonts);
    }

    public function testGetThemePreviewReturnsPath() {
        $controller = new DesignSettingsController();
        $this->assertEquals(
                'content/templates/impro17/screenshot.jpg',
                $controller->_themePreview("impro17")
        );
    }

    public function testThemePreviewReturnsNull() {
        $controller = new DesignSettingsController();
        $this->assertNull($controller->_themePreview("nothing"));
    }

    public function testSetDefaultTheme() {
        $controller = new DesignSettingsController();
        $this->assertNotEquals("foobar", Settings::get("theme"));

        $controller->_setDefaultTheme("foobar");
        $this->assertEquals("foobar", Settings::get("theme"));
    }

    public function testSetDefaultMobileThemeWithTheme() {
        $controller = new DesignSettingsController();
        $this->assertNotEquals("foobar", Settings::get("theme"));

        $controller->_setDefaultMobileTheme("foobar");
        $this->assertEquals("foobar", Settings::get("mobile_theme"));
    }

    public function testSetDefaultMobileThemeWithNull() {
        $controller = new DesignSettingsController();
        $controller->_setDefaultMobileTheme("foobar");
        $controller->_setDefaultMobileTheme("foobar");
        $this->assertNull(Settings::get("mobile_theme"));
    }

}
