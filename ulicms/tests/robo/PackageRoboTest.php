<?php

require_once __DIR__ . "/RoboTestFile.php";
require_once __DIR__ . "/RoboBaseTest.php";

class PackageRoboTest extends RoboBaseTest {
    public function setUp() {
        $this->runRoboCommand(["modules:sync"]);
    }
    
    public function testThemesList() {
        $output = $this->runRoboCommand(["packages:list"]);

        $this->assertEquals(13, substr_count($output, "core_"));

        $this->assertStringContainsString("2020 1.0.1", $output);
        $this->assertStringContainsString("impro17 2.1.4", $output);
    }

}
