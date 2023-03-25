<?php

use App\Services\Connectors\PackageSourceConnector;
use App\Backend\UliCMSVersion;

class UliCMSVersionTest extends \PHPUnit\Framework\TestCase
{
    public function testGetCodeName()
    {
        $version = new UliCMSVersion();
        $this->assertNotEmpty($version->getCodeName());
    }

    public function testDbSchemaVersionSet()
    {
        $version = new UliCMSVersion();
        $this->assertNotEmpty(Settings::get("db_schema_version"));
    }

    public function testGetBuildTimestamp()
    {
        $version = new UliCMSVersion();
        $this->assertIsInt($version->getBuildTimestamp());
    }

    public function testGetBuildDate()
    {
        $version = new UliCMSVersion();

        $date = $version->getBuildDate();

        $this->assertGreaterThanOrEqual(16, strlen($date));
    }

    public function testModuleVersions()
    {
        $modules = getAllModules();
        $ulicmsVersion = (new UliCMSVersion())->getInternalVersionAsString();

        foreach ($modules as $module) {
            $moduleVersion = getModuleMeta($module, "version");
            $this->assertNotEmpty($moduleVersion);
            if (str_starts_with($module, "core_")) {
                $this->assertTrue(
                    \App\Utils\VersionComparison::compare(
                        $moduleVersion,
                        $ulicmsVersion,
                        "="
                    ),
                    "$module has a bad version $moduleVersion"
                );
            }
        }
    }

    public function testCompareModuleVersionsWithPackageSource()
    {
        $modules = getAllModules();
        $connector = new PackageSourceConnector();
        foreach ($modules as $module) {
            $installedVersion = getModuleMeta($module, "version");
            $availableVersion = $connector->getVersionOfPackage($module);

            if ($availableVersion) {
                $this->assertTrue(
                    \App\Utils\VersionComparison::compare(
                        $availableVersion,
                        $installedVersion,
                        ">="
                    ),
                    "$module $availableVersion in the package source "
                    . "is not at least equal to "
                    . "the installed version $module $installedVersion"
                );
            }
        }
    }

    public function testgetYear()
    {
        $version = new UliCMSVersion();
        $year = $version->getReleaseYear();
        $this->assertGreaterThanOrEqual(2020, (int)$year);
        $this->assertLessThanOrEqual(2050, (int)$year);
        $this->assertEquals(4, strlen($year));
    }
}
