<?php

require_once __DIR__ . "/RoboTestFile.php";
require_once __DIR__ . "/RoboBaseTest.php";

class RoboPatchesTest extends RoboBaseTest {

    public function testPatchesAvailable() {
        $output = $this->runRoboCommand(["patches:available"]);
        $this->assertNotEmpty($output);
    }
    public function testPatchesInstalled() {
        $output = $this->runRoboCommand(["patches:installed"]);
        $this->assertNotEmpty($output);
    }

}
