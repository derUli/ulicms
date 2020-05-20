<?php

require_once __DIR__ . "/RoboTestFile.php";
require_once __DIR__ . "/RoboBaseTest.php";

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Robo\TaskAccessor;

class ModulesRoboTest extends RoboBaseTest {

    public function testThemesList() {
        $output = $this->runRoboCommand(["modules:list"]);

        $this->assertEquals(13, substr_count($output, "core_"));
        $this->assertEquals(
                count(getAllModules()),
                substr_count($output, "\n") - 1 
        );
    }

}
