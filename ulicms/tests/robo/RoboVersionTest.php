<?php

require_once __DIR__ . "/RoboTestFile.php";
require_once __DIR__ . "/RoboBaseTest.php";

class RoboVersionTest extends RoboBaseTest
{
    public function testVersion()
    {
        $output = $this->runRoboCommand(["version"]);

        $this->assertStringStartsWith("2023", $output);
        $this->assertStringContainsString(".", $output);
    }
}
