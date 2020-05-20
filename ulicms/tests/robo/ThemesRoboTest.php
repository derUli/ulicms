<?php

require_once __DIR__ . "/RoboTestFile.php";
require_once __DIR__ . "/RoboBaseTest.php";

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Robo\TaskAccessor;

class ThemesRoboTest extends RoboBaseTest {

    public function testThemesList() {
        $output = $this->runRoboCommand(["themes:list"]);
        $this->assertStringContainsString("2020 1.0.1", $output);
        $this->assertStringContainsString("impro17 2.1.4", $output);
    }

}
