<?php

use UliCMS\Constants\PackageTypes;

class PackageManagerTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        $moduleManager = new ModuleManager();
        $moduleManager->sync();
    }

    public function tearDown() {
        $packageManager = new PackageManager();
        $packageManager->truncateInstalledPatches();
    }

    public function testIsInstalledModuleReturnsTrue() {
        $packageManager = new PackageManager();
        $this->assertTrue($packageManager->isInstalled("core_home", PackageTypes::TYPE_MODULE));
    }

    public function testIsInstalledModuleReturnsFalse() {
        $packageManager = new PackageManager();
        $this->assertFalse($packageManager->isInstalled("do_nothing", PackageTypes::TYPE_MODULE));
    }

    public function testIsInstalledModuleThrowsException() {
        $packageManager = new PackageManager();
        $this->expectException(BadMethodCallException::class);
        $packageManager->isInstalled("fortune2", "invalid_type");
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
        for ($i = 1; $i <= 3; $i++) {
            Database::pQuery(
                    "insert into {prefix}installed_patches "
                    . "(name, description, url, date) VALUES "
                    . "(?,?,?, NOW())",
                    [
                        "patch-$i",
                        "Beschreibung $i",
                        "https://google.de",
                    ],
                    true
            );
        }

        $packageManager = new PackageManager();
        $patches = $packageManager->getInstalledPatches();

        $this->assertIsArray($patches);

        $this->assertCount(3, $patches);

        foreach ($patches as $patch) {
            $this->assertNotEmpty($patch->id);
            $this->assertNotEmpty($patch->name);
            $this->assertNotEmpty($patch->description);
            $this->assertNotEmpty($patch->url);
            $this->assertNotEmpty($patch->date);
        }
    }

    public function testGetInstalledPatchNames() {
        for ($i = 1; $i <= 3; $i++) {
            Database::pQuery(
                    "insert into {prefix}installed_patches "
                    . "(name, description, url, date) VALUES "
                    . "(?,?,?, NOW())",
                    [
                        "patch-$i",
                        "Beschreibung $i",
                        "https://google.de",
                    ],
                    true
            );
        }
        $packageManager = new PackageManager();
        $patches = $packageManager->GetInstalledPatchNames();

        $this->assertIsArray($patches);

        foreach ($patches as $patch) {
            $this->assertNotEmpty($patch);
        }
    }

    public function testGetInstalledPackagesWithTypeModules() {
        $packageManager = new PackageManager();
        $packages = $packageManager->getInstalledPackages(
                PackageTypes::TYPE_MODULE);

        $this->greaterThanOrEqual(21, count($packages));

        $this->assertContains("core_content", $packages);
        $this->assertContains("bootstrap", $packages);
        $this->assertContains("fortune2", $packages);
    }

    public function testGetInstalledPackagesWithTypeThemes() {
        $packageManager = new PackageManager();
        $packages = $packageManager->getInstalledPackages(
                PackageTypes::TYPE_THEME);
        $this->assertContains("impro17", $packages);

        $this->assertContains("2020", $packages);
    }

    public function testGetInstalledPackagesWithTypeInvalid() {
        $packageManager = new PackageManager();
        $this->expectException(BadMethodCallException::class);
        $packageManager->getInstalledPackages(
                "invalid_type");
    }

    public function testTruncateInstalledPatches() {
        $packageManager = new PackageManager();
        $packageManager->truncateInstalledPatches();
        $query = Database::selectAll("installed_patches");
        $this->assertFalse(Database::any($query));
    }

}
