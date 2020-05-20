<?php

require_once __DIR__ . "/RoboTestFile.php";
require_once __DIR__ . "/RoboBaseTest.php";

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Robo\TaskAccessor;

class MaintenanceRoboTest extends RoboBaseTest {

    public function testMaintenanceStatus() {
        $this->runRoboCommand(["maintenance:on"]);

        $this->assertStringStartsWith("true", $this->runRoboCommand(["maintenance:status"]));
        $this->runRoboCommand(["maintenance:off"]);
        $this->assertStringStartsWith("false", $this->runRoboCommand(["maintenance:status"]));
    }

}
