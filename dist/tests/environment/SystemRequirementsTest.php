<?php

use App\Helpers\TestHelper;

class SystemRequirementsTest extends \PHPUnit\Framework\TestCase {
    public function testPhpVersion(): void {
        $this->assertTrue(\App\Utils\VersionComparison::compare(PHP_VERSION, '8.1.0', '>='));
    }

    public function testMySQLVersion(): void {
        $this->assertTrue(
            \App\Utils\VersionComparison::compare($this->getMySQLVersion(), '5.5.3', '>=')
        );
    }

    public function testRootDirWritable() {
        $this->assertDirectoryIsWritable(ULICMS_ROOT);
    }

    public function testPhpModuleMySqli(): void {
        $this->assertTrue(extension_loaded('mysqli'));
    }

    public function testPhpModuleGd(): void {
        $this->assertTrue(extension_loaded('gd'));
    }

    public function testPhpModuleJson(): void {
        $this->assertTrue(extension_loaded('json'));
    }

    public function testPhpModuleMbString(): void {
        $this->assertTrue(extension_loaded('mbstring'));
    }

    public function testPhpModuleOpenSSL(): void {
        $this->assertTrue(extension_loaded('openssl'));
    }

    public function testPhpModuleDom(): void {
        $this->assertTrue(extension_loaded('dom'));
    }

    public function testPhpModuleXml(): void {
        $this->assertTrue(extension_loaded('xml'));
    }

    public function testPhpModuleIntl(): void {
        $this->assertTrue(extension_loaded('intl'));
    }

    public function testPhpModuleCurl(): void {
        $this->assertTrue(extension_loaded('curl'));
    }

    public function testConnectToUliCMSServices(): void {
        $this->assertNotNull(file_get_contents_wrapper('https://www.ulicms.de/', true));
    }

    public function testPhpIcoInstalled(): void {
        $this->assertTrue(class_exists('PHP_ICO'));
    }

    public function testIsRunningPHPUnit(): void {
        $this->assertTrue(TestHelper::isRunningPHPUnit());
    }

    private function getMySQLVersion() {
        $version = Database::getServerVersion();
        $version = preg_replace('/[^0-9.].*/', '', $version);
        return $version;
    }
}
