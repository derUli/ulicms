<?php

use App\Packages\SinPackageInstaller;

class SinPackageInstallerTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        require_once getLanguageFilePath('en');
    }

    public function testIsInstallableReturnsTrue()
    {
        $installer = $this->getSinPackageInstaller('is_proxy-1.0.sin');
        $this->assertTrue($installer->isInstallable());
    }

    public function testIsInstallableReturnsFalse()
    {
        $installer = $this->getSinPackageInstaller('ip_api_com-1.0.sin');
        $this->assertFalse($installer->isInstallable());
    }

    public function testGetSize()
    {
        $installer = $this->getSinPackageInstaller('is_proxy-1.0.sin');
        $this->assertEquals(671, $installer->getSize());
    }

    public function testGetProperty()
    {
        $installer = $this->getSinPackageInstaller('is_proxy-1.0.sin');
        $this->assertEquals('Check if site is accessed by a proxy server', $installer->getProperty('description'));

        $this->assertEquals('2018.1', $installer->getProperty('compatible_from'));

        $this->assertEquals(1563134217, $installer->getProperty('build_date'));

        $this->assertNull($installer->getProperty('gibts_nicht'));
    }

    public function testGetErrorsReturnsNothing()
    {
        $installer = $this->getSinPackageInstaller('is_proxy-1.0.sin');
        $installer->isInstallable();
        $this->assertIsArray($installer->getErrors());
        $this->assertCount(0, $installer->getErrors());
    }

    public function testGetErrorsReturnsErrors1()
    {
        $installer = $this->getSinPackageInstaller('ip_api_com-1.0.sin');
        $installer->isInstallable();
        $errors = $installer->getErrors();
        $this->assertIsArray($errors);
        $this->assertCount(2, $errors);
        $phpversion = PHP_VERSION;
        $this->assertEquals("The PHP version {$phpversion} is not supported.", $errors[0]);
        $this->assertEquals('The package is not compatible with your UliCMS Version.', $errors[1]);
    }

    public function testGetErrorsReturnsErrors2()
    {
        $installer = $this->getSinPackageInstaller('hello_world-1.0.incompatible.sin');

        $this->assertFalse($installer->isInstallable());

        $errors = $installer->getErrors();
        $this->assertStringContainsString('Depedency foo is not installed.', $errors[0]);
        $this->assertStringContainsString('Depedency bar is not installed.', $errors[1]);

        $this->assertStringContainsString('The PHP version', $errors[2]);
        $this->assertStringContainsString('is not supported.', $errors[2]);

        $this->assertStringContainsString('The MySQL version', $errors[3]);
        $this->assertStringContainsString('is not supported.', $errors[3]);

        $this->assertStringContainsString('The required php extension foo is not installed.', $errors[4]);

        $this->assertStringContainsString('The package is not compatible with your UliCMS Version.', $errors[5]);

        $this->assertStringContainsString('SHA1 checksums are not equal.', $errors[6]);
    }

    public function testLoadPackage()
    {
        $installer = $this->getSinPackageInstaller('is_proxy-1.0.sin');
        $data = $installer->loadPackage();
        $this->assertIsArray($data);
        $this->assertArrayHasKey('version', $data);
        $this->assertArrayHasKey('license', $data);
        $this->assertArrayHasKey('checksum', $data);
        $this->assertArrayHasKey('data', $data);
    }

    private function getSinPackageInstaller($file)
    {
        return new SinPackageInstaller(
            Path::resolve(
                "ULICMS_ROOT/tests/fixtures/packages/{$file}"
            )
        );
    }
}
