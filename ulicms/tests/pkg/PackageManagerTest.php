<?php

use UliCMS\Constants\PackageTypes;

class PackageManagerTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        $moduleManager = new ModuleManager();
        $moduleManager->sync();
    }

    public function testIsInstalledModuleReturnsTrue() {
        $packageManager = new PackageManager();
        $this->assertTrue($packageManager->isInstalled("core_home", PackageTypes::TYPE_MODULE));
    }

    public function testIsInstalledModuleReturnsFalse() {
        $packageManager = new PackageManager();
        $this->assertFalse($packageManager->isInstalled("do_nothing", PackageTypes::TYPE_MODULE));
    }

    public function testIsInstalledThemeReturnsTrue() {
        $packageManager = new PackageManager();
        $this->assertTrue($packageManager->isInstalled("impro17", PackageTypes::TYPE_THEME));
        $this->assertTrue($packageManager->isInstalled("2020", PackageTypes::TYPE_THEME));
    }

    public function testIsInstalledThemeReturnsFalse() {
        $packageManager = new PackageManager();
        $this->assertFalse($packageManager->isInstalled("my_ugly_theme", PackageTypes::TYPE_THEME));
    }

    public function testSplitPackageName() {
        $packageManager = new PackageManager();
        $input = "foo-bar-2.0";
        $expected = [
            "foo-bar",
            "2.0"
        ];
        $this->assertEquals($expected, $packageManager->splitPackageName($input));
    }

    public function testCheckForNewerVersionPackageReturnsFalse() {
        $packageManager = new PackageManager();
        $this->assertEquals(
                "3.3.7",
                $packageManager->checkForNewerVersionOfPackage("bootstrap")
        );
    }

    public function testGetInstalledPatches() {
        $packageManager = new PackageManager();
        $patches = $packageManager->getInstalledPatches();

        $this->assertIsArray($patches);

        foreach ($patches as $patch) {
            $this->assertNotEmpty($patch->id);
            $this->assertNotEmpty($patch->name);
            $this->assertNotEmpty($patch->description);
            $this->assertNotEmpty($patch->url);
            $this->assertNotEmpty($patch->date);
        }
    }

    public function testGetInstalledPatchNames() {
        $packageManager = new PackageManager();
        $patches = $packageManager->GetInstalledPatchNames();

        $this->assertIsArray($patches);

        foreach ($patches as $patch) {
            $this->assertNotEmpty($patch);
        }
    }

}
