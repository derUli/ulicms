<?php

use App\Constants\PackageTypes;

class PackageManagerTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $moduleManager = new ModuleManager();
        $moduleManager->sync();
    }

    public function testIsInstalledModuleReturnsTrue()
    {
        $packageManager = new PackageManager();
        $this->assertTrue($packageManager->isInstalled("core_home", PackageTypes::TYPE_MODULE));
    }

    public function testIsInstalledModuleReturnsFalse()
    {
        $packageManager = new PackageManager();
        $this->assertFalse($packageManager->isInstalled("do_nothing", PackageTypes::TYPE_MODULE));
    }

    public function testIsInstalledModuleThrowsException()
    {
        $packageManager = new PackageManager();
        $this->expectException(BadMethodCallException::class);
        $packageManager->isInstalled("fortune2", "invalid_type");
    }

    public function testIsInstalledThemeReturnsTrue()
    {
        $packageManager = new PackageManager();
        $this->assertTrue($packageManager->isInstalled("impro17", PackageTypes::TYPE_THEME));
        $this->assertTrue($packageManager->isInstalled("2020", PackageTypes::TYPE_THEME));
    }

    public function testIsInstalledThemeReturnsFalse()
    {
        $packageManager = new PackageManager();
        $this->assertFalse($packageManager->isInstalled("my_ugly_theme", PackageTypes::TYPE_THEME));
    }

    public function testCheckForNewerVersionPackageReturnsFalse()
    {
        $packageManager = new PackageManager();
        $this->assertEquals(
            "3.3.7",
            $packageManager->checkForNewerVersionOfPackage("bootstrap")
        );
    }

    public function testGetInstalledPackagesWithTypeModules()
    {
        $packageManager = new PackageManager();
        $packages = $packageManager->getInstalledPackages(
            PackageTypes::TYPE_MODULE
        );

        $this->greaterThanOrEqual(21, count($packages));

        $this->assertContains("core_content", $packages);
        $this->assertContains("bootstrap", $packages);
        $this->assertContains("fortune2", $packages);
    }

    public function testGetInstalledPackagesWithTypeThemes()
    {
        $packageManager = new PackageManager();
        $packages = $packageManager->getInstalledPackages(
            PackageTypes::TYPE_THEME
        );
        $this->assertContains("impro17", $packages);

        $this->assertContains("2020", $packages);
    }

    public function testGetInstalledPackagesWithTypeInvalid()
    {
        $packageManager = new PackageManager();
        $this->expectException(BadMethodCallException::class);
        $packageManager->getInstalledPackages(
            "invalid_type"
        );
    }

    public function testInstallTheme2017()
    {
        $packageFile = Path::resolve(
            "ULICMS_ROOT/tests/fixtures/packages/theme-2017-1.1.1.tar.gz"
        );

        $installer = new PackageManager();
        $success = $installer->installPackage($packageFile);

        $this->assertTrue($success);
        $this->assertContains("2017", getAllThemes());
    }

    public function testInstallThemeBroken()
    {
        $packageFile = Path::resolve(
            "ULICMS_ROOT/tests/fixtures/packages/broken.tar.gz"
        );

        $installer = new PackageManager();
        $success = $installer->installPackage($packageFile);

        $this->assertFalse($success);
    }
}
