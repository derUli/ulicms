<?php

class SystemRequirementsTest extends \PHPUnit\Framework\TestCase
{

    public function testPhpVersion()
    {
        $this->assertTrue(version_compare(phpversion(), "5.6", ">="));
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
        $this->assertNotFalse(file_get_contents_wrapper("https://www.ulicms.de/", true));
    }
}