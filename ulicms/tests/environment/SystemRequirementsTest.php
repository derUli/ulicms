<?php

use App\Helpers\TestHelper;

class SystemRequirementsTest extends \PHPUnit\Framework\TestCase
{
    public function testPhpVersion()
    {
        $this->assertTrue(\App\Utils\VersionComparison::compare(phpversion(), "8.0.2", ">="));
    }

    public function testMySQLVersion()
    {
        $this->assertTrue(
            \App\Utils\VersionComparison::compare($this->getMySQLVersion(), "5.5.3", '>=')
        );
    }

    private function getMySQLVersion()
    {
        $version = Database::getServerVersion();
        $version = preg_replace('/[^0-9.].*/', '', $version);
        return $version;
    }

    public function testPhpModuleMySqli()
    {
        $this->assertTrue(extension_loaded("mysqli"));
    }

    public function testPhpModuleGd()
    {
        $this->assertTrue(extension_loaded("gd"));
    }

    public function testPhpModuleJson()
    {
        $this->assertTrue(extension_loaded("json"));
    }

    public function testPhpModuleMbString()
    {
        $this->assertTrue(extension_loaded("mbstring"));
    }

    public function testPhpModuleOpenSSL()
    {
        $this->assertTrue(extension_loaded("openssl"));
    }

    public function testPhpModuleDom()
    {
        $this->assertTrue(extension_loaded("dom"));
    }

    public function testPhpModuleXml()
    {
        $this->assertTrue(extension_loaded("xml"));
    }

    public function testConnectToUliCMSServices()
    {
        $this->assertNotNull(file_get_contents_wrapper("https://www.ulicms.de/", true));
    }

    public function testPhpIcoInstalled()
    {
        $this->assertTrue(class_exists("PHP_ICO"));
    }

    public function testIsRunningPHPUnit()
    {
        $this->assertTrue(TestHelper::isRunningPHPUnit());
    }
}
