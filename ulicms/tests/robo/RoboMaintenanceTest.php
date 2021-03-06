<?php

require_once __DIR__ . "/RoboTestFile.php";
require_once __DIR__ . "/RoboBaseTest.php";

class RoboMaintenanceTest extends RoboBaseTest
{
    public function testMaintenanceStatus()
    {
        $this->runRoboCommand(["maintenance:on"]);

        $this->assertStringStartsWith("true", $this->runRoboCommand(["maintenance:status"]));
        $this->runRoboCommand(["maintenance:off"]);
        $this->assertStringStartsWith("false", $this->runRoboCommand(["maintenance:status"]));
    }
}
