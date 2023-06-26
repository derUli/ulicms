<?php

class LogoControllerTest extends \PHPUnit\Framework\TestCase {
    private $initialSettings = [];

    protected function setUp(): void {
        $this->initialSettings = [
            'logo_disabled' => Settings::get('logo_disabled'),
            'logo_image' => Settings::get('logo_image'),
        ];
        $controller = new LogoController();

        $fixtureFile = $this->getFixturePath();
        $filePath = $controller->_buildFilePath(
            $fixtureFile,
            'cat.jpg'
        );
        copy($fixtureFile, $filePath);

        Settings::set('logo_disabled', 'no');
        Settings::set('logo_image', basename($filePath));
    }

    protected function tearDown(): void {
        foreach ($this->initialSettings as $key => $value) {
            Settings::set($key, $value);
        }

        $controller = new LogoController();
        $fixtureFile = $this->getFixturePath();
        $fileName = $controller->_buildFilePath(
            $fixtureFile,
            'cat.jpg'
        );

        if (is_file($fileName)) {
            unlink($fileName);
        }
    }

    public function testDeleteLogo(): void {
        $controller = new LogoController();
        $this->assertEquals('no', Settings::get('logo_disabled'));
        $this->assertTrue($controller->_deleteLogo());

        $this->assertEmpty(Settings::get('logo_image'));
        $this->assertEquals('yes', Settings::get('logo_disabled'));
        $this->assertFalse($controller->_deleteLogo());
    }

    public function testHasLogoReturnsTrue(): void {
        $controller = new LogoController();
        $this->assertTrue($controller->_hasLogo());
    }

    public function testHasLogoReturnsFalse(): void {
        $controller = new LogoController();
        $controller->_deleteLogo();
        $this->assertFalse($controller->_hasLogo());
    }

    public function testBuildFileName(): void {
        $controller = new LogoController();

        $fileName = $controller->_buildFileName(
            $this->getFixturePath(),
            'cat.jpg'
        );
        $this->assertEquals('61edbeb9410bc87763cebc93fbba8335.jpg', $fileName);
    }

    public function testBuildFilePath(): void {
        $controller = new LogoController();

        $filePath = $controller->_buildFilePath(
            $this->getFixturePath(),
            'cat.jpg'
        );

        $this->assertStringEndsWith(
            '/content/images/61edbeb9410bc87763cebc93fbba8335.jpg',
            $filePath
        );
    }

    protected function getFixturePath(): string {
        return \App\Utils\Path::Resolve('ULICMS_ROOT/tests/fixtures/cat.jpg');
    }
}
