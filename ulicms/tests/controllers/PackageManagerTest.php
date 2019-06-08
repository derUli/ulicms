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
    }

    public function testIsInstalledThemeReturnsFalse() {
        $packageManager = new PackageManager();
        $this->assertFalse($packageManager->isInstalled("my_ugly_theme", PackageTypes::TYPE_THEME));
    }

}
