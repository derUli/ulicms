<?php

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
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    public function testGenerateSCSS() {
        $controller = ControllerRegistry::get(DesignSettingsController::class);
        $scss = $controller->removeCommentFromCss($controller->GenerateSCSS());
        $this->assertGreaterThanOrEqual(5, substr_count($scss, "\n"));
        $lines = explode("\n", trim(normalizeLN($scss, "\n")));
        foreach ($lines as $line) {
            $this->assertRegExp('/\$(.+): (.+);/', $line);
        }
    }

    function testGenerateSCSSToFile() {
        $controller = ControllerRegistry::get(DesignSettingsController::class);
        $file = $controller->generateSCSSToFile();
        $this->assertStringEndsWith("/content/generated/design_variables.scss", $file);
        $this->assertFileExists($file);

        $fileContent = $controller->removeCommentFromCss(file_get_contents($file));

        $this->assertGreaterThanOrEqual(5, substr_count($fileContent, "\n"));
    }

}
