<?php

class LogoUploadControllerTest extends \PHPUnit\Framework\TestCase {

    private $initialSettings = [];

    protected function setUp(): void {
        $this->initialSettings = [
            "logo_disabled" => Settings::get("logo_disabled"),
            "logo_image" => Settings::get("logo_image"),
        ];
        $controller = new LogoUploadController();

        $fixtureFile = $this->getFixturePath();
        $filePath = $controller->_buildFilePath(
                $fixtureFile,
                "cat.jpg"
        );
        copy($fixtureFile, $filePath);
        
        Settings::set("logo_disabled", "no");
        Settings::set("logo_image", basename($filePath));
    }

    protected function tearDown(): void {
        foreach ($this->initialSettings as $key => $value) {
            Settings::set($key, $value);
        }

        $controller = new LogoUploadController();
        $fixtureFile = $this->getFixturePath();
        $fileName = $controller->_buildFilePath(
                $fixtureFile,
                "cat.jpg"
        );
        if (file_exists($fileName)) {
            unlink($fileName);
        }
    }

    protected function getFixturePath(): string {
        return Path::Resolve("ULICMS_ROOT/tests/fixtures/cat.jpg");
    }

    public function testDeleteLogo() {
        $controller = new LogoUploadController();
        $this->assertEquals("no", Settings::get("logo_disabled"));
        $this->assertTrue($controller->_deleteLogo());

        $this->assertEmpty(Settings::get("logo_image"));
        $this->assertEquals("yes", Settings::get("logo_disabled"));
        $this->assertFalse($controller->_deleteLogo());
    }

    public function testBuildFileName() {
        $controller = new LogoUploadController();

        $fileName = $controller->_buildFileName(
                $this->getFixturePath(),
                "cat.jpg"
        );
        $this->assertEquals("94f7fbd93d43a9f6b026f4b712d48be7.jpg", $fileName);
    }

    public function testBuildFilePath() {
        $controller = new LogoUploadController();

        $filePath = $controller->_buildFilePath(
                $this->getFixturePath(),
                "cat.jpg"
        );
        $this->assertStringEndsWith(
                "/content/images/94f7fbd93d43a9f6b026f4b712d48be7.jpg",
                $filePath
        );
    }

}
