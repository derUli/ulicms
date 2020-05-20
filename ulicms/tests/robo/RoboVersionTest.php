<?php

require_once __DIR__."/RoboTestFile.php";
require_once __DIR__."/RoboBaseTest.php";

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Robo\TaskAccessor;

class RoboVersionTest extends RoboBaseTest {


    public function testVersion() {
        $output = $this->runRoboCommand(["version"]);
       
        $this->assertStringStartsWith("2020", $output);
        $this->assertStringContainsString(".", $output);
    }

}
