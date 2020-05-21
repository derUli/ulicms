<?php

require_once __DIR__ . "/RoboTestFile.php";
require_once __DIR__ . "/RoboBaseTest.php";

class RoboThemesTest extends RoboBaseTest {
    public function setUp() {
        $this->runRoboCommand(["modules:sync"]);
        $this->runRoboCommand(["cache:clear"]);
    }
    
    public function testThemesList() {
        $output = $this->runRoboCommand(["themes:list"]);
        $this->assertStringContainsString("2020 1.0.1", $output);
        $this->assertStringContainsString("impro17 2.1.4", $output);
    }

    public function testModulesRemoveReturnsError() {
        $actual = $this->runRoboCommand(
                [
                    "themes:remove",
                    "foobar1",
                    "foobar2"
                ]
        );
        $this->assertStringContainsString("Removing foobar1 failed.", $actual);
        $this->assertStringContainsString("Removing foobar2 failed.", $actual);
    }
}
