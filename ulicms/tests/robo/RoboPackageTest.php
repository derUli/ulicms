<?php

require_once __DIR__ . "/RoboTestFile.php";
require_once __DIR__ . "/RoboBaseTest.php";

class RoboPackageTest extends RoboBaseTest
{
    protected function setUp(): void
    {
        $this->runRoboCommand(["modules:sync"]);
    }

    protected function tearDown(): void
    {
        $moduleDir = Path::resolve("ULICMS_ROOT/content/modules/hello_world");
        if (is_dir($moduleDir)) {
            sureRemoveDir($moduleDir);
        }
    }

    public function testPackagesList()
    {
        $output = $this->runRoboCommand(["packages:list"]);

        $this->assertEquals(13, substr_count($output, "core_"));

        $this->assertStringContainsString("2020 1.0.4", $output);
        $this->assertStringContainsString("impro17 2.1.4", $output);
    }

    public function testPackageExamineReturnsData()
    {
        $packageFile = Path::resolve(
            "ULICMS_ROOT/tests/fixtures/packages/lock_inactive_users-1.0.1.sin"
        );

        $expected = file_get_contents(
            Path::resolve(
                "ULICMS_ROOT/tests/fixtures/packages/packageExamine.expected.txt"
            )
        );

        $output = $this->runRoboCommand(["package:examine", $packageFile]);

        $this->assertEquals(
            trim(normalizeLN($expected)),
            trim(normalizeLN($output))
        );
    }

    public function testPackageExamineReturnsError()
    {
        $output = $this->runRoboCommand(
            ["package:examine", "../magic-1.0.sin"]
        );
        $this->assertEquals("File magic-1.0.sin not found!", $output);
    }

    public function testPackagesInstallReturnsError()
    {
        $output = $this->runRoboCommand(
            ["package:install", "../magic-1.0.sin"]
        );
        $this->assertEquals("Can't open ../magic-1.0.sin. File doesn't exists.", $output);
    }

    public function testPackageInstallWithSinFile()
    {
        $packageFile = Path::resolve(
            "ULICMS_ROOT/tests/fixtures/packages/hello_world-1.0.sin"
        );
        $installOutput = $this->runRoboCommand(
            ["package:install", $packageFile]
        );
        $this->assertEquals(
            "Package hello_world-1.0.sin successfully installed",
            $installOutput
        );

        Vars::delete("allModules");
        $this->assertContains("hello_world", getAllModules());

        $removeOutput = $this->runRoboCommand(
            ["modules:remove", "hello_world"]
        );

        Vars::delete("allModules");
        $this->assertEquals("Package hello_world removed.", $removeOutput);
        $this->assertNotContains("hello_world", getAllModules());
    }

    public function testPackageInstallWithTarGzFile()
    {
        $packageFile = Path::resolve(
            "ULICMS_ROOT/tests/fixtures/packages/hello_world-1.0.tar.gz"
        );
        $installOutput = $this->runRoboCommand(
            ["package:install", $packageFile]
        );
        $this->assertEquals(
            "Package hello_world-1.0.tar.gz successfully installed",
            $installOutput
        );

        Vars::delete("allModules");
        $this->assertContains("hello_world", getAllModules());
    }

    public function testPackageInstallReturnsError()
    {
        $packageFile = Path::resolve(
            "ULICMS_ROOT/tests/fixtures/packages/error-1.0.sin"
        );
        $output = $this->runRoboCommand(
            ["package:install", $packageFile]
        );

        $this->assertStringContainsString(
            "Installation of package error-1.0.sin failed.",
            $output
        );
        $this->assertStringContainsString(
            "Depedency foobar is not installed.",
            $output
        );
        $this->assertStringContainsString(
            "The package is not with your UliCMS Version compatible.",
            $output
        );
    }
}
