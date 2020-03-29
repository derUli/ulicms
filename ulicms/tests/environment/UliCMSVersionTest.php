<?php

class UliCMSVersionTest extends \PHPUnit\Framework\TestCase {

    public function testGetCodeName() {
        $version = new UliCMSVersion();
        $this->assertNotEmpty($version->getCodeName());
    }

    public function testGetBuildTimestamp() {
        $version = new UliCMSVersion();
        $this->assertIsInt($version->getBuildTimestamp());
    }

    public function testGetBuildDate() {
        $version = new UliCMSVersion();

        $date = $version->getBuildDate();

        $this->assertGreaterThanOrEqual(16, strlen($date));
    }

    public function testModuleVersions() {
        $modules = getAllModules();
        $ulicmsVersion = (new UliCMSVersion())->getInternalVersionAsString();

        foreach ($modules as $module) {
            $moduleVersion = getModuleMeta($module, "version");
            $this->assertNotEmpty($moduleVersion);
            if(startsWith($module, "core_")){
                $this->assertTrue(
                        version_compare(
                                $moduleVersion,
                                $ulicmsVersion,
                                ">="
                                ), "$module has a bad version $moduleVersion");
            }
        }
    }

}
