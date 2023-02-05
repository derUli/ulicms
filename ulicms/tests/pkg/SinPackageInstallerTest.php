<?php

use UliCMS\Exceptions\NotImplementedException;

class SinPackageInstallerTest extends \PHPUnit\Framework\TestCase {

    protected function setUp(): void {
        require_once getLanguageFilePath("en");
    }

    private function getSinPackageInstaller($file) {
        return new SinPackageInstaller(
                Path::resolve(
                        "ULICMS_ROOT/tests/fixtures/packages/{$file}"
                )
        );
    }

    public function testIsInstallableReturnsTrue() {
        $installer = $this->getSinPackageInstaller("is_proxy-1.0.sin");
        $this->assertTrue($installer->isInstallable());
    }

    public function testIsInstallableReturnsFalse() {
        $installer = $this->getSinPackageInstaller("ip_api_com-1.0.sin");
        $this->assertFalse($installer->isInstallable());
    }

    public function testGetSize() {
        $installer = $this->getSinPackageInstaller("is_proxy-1.0.sin");
        $this->assertEquals(671, $installer->getSize());
    }

    public function testGetProperty() {
        $installer = $this->getSinPackageInstaller("is_proxy-1.0.sin");
        $this->assertEquals("Check if site is accessed by a proxy server", $installer->getProperty("description"));

        $this->assertEquals("2018.1", $installer->getProperty("compatible_from"));

        $this->assertEquals(1563134217, $installer->getProperty("build_date"));

        $this->assertNull($installer->getProperty('gibts_nicht'));
    }

    public function testGetPropertyCompressed() {
        $installer = $this->getSinPackageInstaller("is_proxy-2.0.sin2");
        $this->assertEquals("Check if site is accessed by a proxy server", $installer->getProperty("description"));

        $this->assertFalse($installer->isInstallable());
        $this->assertEquals("2.0", $installer->getProperty("version"));
    }

    public function testGetErrorsReturnsNothing() {
        $installer = $this->getSinPackageInstaller("is_proxy-1.0.sin");
        $installer->isInstallable();
        $this->assertIsArray($installer->getErrors());
        $this->assertCount(0, $installer->getErrors());
    }

    public function testGetErrorsReturnsErrors() {
        $installer = $this->getSinPackageInstaller("ip_api_com-1.0.sin");
        $installer->isInstallable();
        $errors = $installer->getErrors();
        $this->assertIsArray($errors);
        $this->assertCount(2, $errors);
        $phpversion = phpversion();
        $this->assertEquals("The PHP version {$phpversion} is not supported.", $errors[0]);
        $this->assertEquals("The package is not with your UliCMS Version compatible.", $errors[1]);
    }

    public function testLoadPackage() {
        $installer = $this->getSinPackageInstaller("is_proxy-1.0.sin");
        $data = $installer->loadPackage();
        $this->assertIsArray($data);
        $this->assertArrayHasKey("version", $data);
        $this->assertArrayHasKey("license", $data);
        $this->assertArrayHasKey("checksum", $data);
        $this->assertArrayHasKey("data", $data);
    }

}
