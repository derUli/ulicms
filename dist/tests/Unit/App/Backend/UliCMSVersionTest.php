<?php

use App\Backend\UliCMSVersion;
use App\Services\Connectors\PackageSourceConnector;

class UliCMSVersionTest extends \PHPUnit\Framework\TestCase {
    public function testGetCodeName(): void {
        $version = new UliCMSVersion();
        $this->assertNotEmpty($version->getCodeName());
    }

    public function testDbSchemaVersionSet(): void {
        $version = new UliCMSVersion();
        $this->assertNotEmpty(Settings::get('db_schema_version'));
    }

    public function testGetBuildTimestamp(): void {
        $version = new UliCMSVersion();
        $this->assertIsInt($version->getBuildTimestamp());
    }

    public function testGetBuildDate(): void {
        $version = new UliCMSVersion();

        $date = $version->getBuildDate();

        $this->assertGreaterThanOrEqual(16, strlen($date));
    }

    public function testModuleVersions(): void {
        $modules = getAllModules();
        $ulicmsVersion = (new UliCMSVersion())->getInternalVersionAsString();

        foreach ($modules as $module) {
            $moduleVersion = getModuleMeta($module, 'version');
            $this->assertNotEmpty($moduleVersion);
            if (str_starts_with($module, 'core_')) {
                $this->assertTrue(
                    \App\Utils\VersionComparison::compare(
                        $moduleVersion,
                        $ulicmsVersion,
                        '='
                    ),
                    "{$module} has a bad version {$moduleVersion}"
                );
            }
        }
    }

    public function testCompareModuleVersionsWithPackageSource(): void {
        $modules = getAllModules();
        $connector = new PackageSourceConnector();
        foreach ($modules as $module) {
            $installedVersion = getModuleMeta($module, 'version');
            $availableVersion = $connector->getVersionOfPackage($module);

            if ($availableVersion) {
                $this->assertTrue(
                    \App\Utils\VersionComparison::compare(
                        $availableVersion,
                        $installedVersion,
                        '>='
                    ),
                    "{$module} {$availableVersion} in the package source "
                    . 'is not at least equal to '
                    . "the installed version {$module} {$installedVersion}"
                );
            }
        }
    }

    public function testgetYear(): void {
        $version = new UliCMSVersion();
        $year = $version->getReleaseYear();
        $this->assertGreaterThanOrEqual(2020, (int)$year);
        $this->assertLessThanOrEqual(2050, (int)$year);
        $this->assertEquals(4, strlen($year));
    }
}
