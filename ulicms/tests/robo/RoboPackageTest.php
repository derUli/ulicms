<?php

require_once __DIR__ . "/RoboTestFile.php";
require_once __DIR__ . "/RoboBaseTest.php";

class RoboPackageTest extends RoboBaseTest {

    public function setUp() {
        $this->runRoboCommand(["modules:sync"]);
    }

    public function testPackagesList() {
        $output = $this->runRoboCommand(["packages:list"]);

        $this->assertEquals(13, substr_count($output, "core_"));

        $this->assertStringContainsString("2020 1.0.1", $output);
        $this->assertStringContainsString("impro17 2.1.4", $output);
    }

    public function testPackageExamineReturnsData() {
        $packageFile = Path::resolve(
                        "ULICMS_ROOT/tests/fixtures/lock_inactive_users-1.0.1.sin"
        );

        $expected = file_get_contents(
                Path::resolve(
                        "ULICMS_ROOT/tests/fixtures/packageExamine.expected.txt"
                )
        );

        $output = $this->runRoboCommand(["package:examine", $packageFile]);

        $this->assertEquals(
                trim(normalizeLN($expected)),
                trim(normalizeLN($output))
        );
    }

    public function testPackageExamineReturnsError() {
        $output = $this->runRoboCommand(
                ["package:examine", "../magic-1.0.sin"]
        );
        $this->assertEquals("File magic-1.0.sin not found!", $output);
    }

    public function testPackagesInstallReturnsError() {
        $output = $this->runRoboCommand(
                ["package:install", "../magic-1.0.sin"]
        );
        $this->assertEquals("Can't open ../magic-1.0.sin. File doesn't exists.", $output);
    }

}
