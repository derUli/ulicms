<?php

use UliCMS\Utils\File;

class DesignSettingsControllerTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        $this->cleanUpFiles();
    }

    public function tearDown() {
        $this->cleanUpFiles();
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

}
