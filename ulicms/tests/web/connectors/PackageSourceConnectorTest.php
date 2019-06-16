<?php

use UliCMS\Services\Connectors\PackageSourceConnector;

class PackageSourceConnectorTest extends \PHPUnit\Framework\TestCase {

    const VALID_PACKAGE_SOURCE_URL = "https://packages.ulicms.de/2019.2.6/";
    const INVALID_PACKAGE_SOURCE_URL = "https://gibts-nicht.de/";

    public function testFetchWithInvalidUrl() {
        $connector = new PackageSourceConnector(self::INVALID_PACKAGE_SOURCE_URL);
        $this->assertFalse($connector->fetch());
    }

    public function testFetchWithValidUrl() {
        $connector = new PackageSourceConnector(self::VALID_PACKAGE_SOURCE_URL);
        $this->assertTrue($connector->fetch());
    }

    public function testGetAllAvailablePackages() {
        $connector = new PackageSourceConnector(self::VALID_PACKAGE_SOURCE_URL);
        $this->assertCount(144, $connector->getAllAvailablePackages());
    }

    public function testGetPackageVersionReturnsString() {
        $connector = new PackageSourceConnector(self::VALID_PACKAGE_SOURCE_URL);
        $this->assertEquals("2.0.1", $connector->getVersionOfPackage("facebook_sdk"));
    }

    public function testGetPackageVersionReturnsNull() {
        $connector = new PackageSourceConnector(self::VALID_PACKAGE_SOURCE_URL);
        $this->assertNull($connector->getVersionOfPackage("gibts_nicht"));
    }

    public function testGetPackageLicenseReturnsString() {
        $connector = new PackageSourceConnector(self::VALID_PACKAGE_SOURCE_URL);
        $license = $connector->getLicenseOfPackage("IXR_Library");
        $this->assertContains("Copyright (c) 2010, Incutio Ltd.", $license);
        $this->assertContains("Redistributions of source code must retain the above copyright notice", $license);
    }

    public function testGetPackageLicenseReturnsNull() {
        $connector = new PackageSourceConnector(self::VALID_PACKAGE_SOURCE_URL);
        $this->assertNull($connector->getLicenseOfPackage("gibts_nicht"));
    }

    public function testGetPackageDataReturnsObjectTypeTheme() {
        $connector = new PackageSourceConnector(self::VALID_PACKAGE_SOURCE_URL);
        $metadata = $connector->getDataOfPackage("theme-2017");
        $this->assertInstanceOf(stdClass::class, $metadata);
        $this->assertEquals("theme-2017", $metadata->name);
        $this->assertEquals("theme", $metadata->type);
        $this->assertEquals("1.0.4", $metadata->version);
    }

    public function testGetPackageDataReturnsObjectTypeModule() {

        $connector = new PackageSourceConnector(self::VALID_PACKAGE_SOURCE_URL);
        $metadata = $connector->getDataOfPackage("break_frames");
        $this->assertInstanceOf(stdClass::class, $metadata);
        $this->assertEquals("break_frames", $metadata->name);
        $this->assertEquals("module", $metadata->type);
        $this->assertEquals("0.0.2", $metadata->version);
    }

    public function testGetPackageDataReturnsNull() {
        $connector = new PackageSourceConnector(self::VALID_PACKAGE_SOURCE_URL);
        $this->assertNull($connector->getDataOfPackage("gibts_nicht"));
    }

    public function testFetchPackagesWithDefaultPackageSource() {
        $connector = new PackageSourceConnector();
        $this->assertTrue($connector->fetch(true));

        $metadata = $connector->getDataOfPackage("break_frames");

        $this->assertInstanceOf(stdClass::class, $metadata);
        $this->assertEquals("break_frames", $metadata->name);
        $this->assertEquals("module", $metadata->type);
        $this->assertEquals("0.0.2", $metadata->version);
    }

    public function testGetPackageSourceUrl() {
        $connector = new PackageSourceConnector(self::VALID_PACKAGE_SOURCE_URL);
        $this->assertEquals("https://packages.ulicms.de/2019.2.6/index.json",
                $connector->getPackageSourceUrl());
    }

}
