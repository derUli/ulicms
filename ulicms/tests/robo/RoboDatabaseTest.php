<?php

require_once __DIR__ . "/RoboTestFile.php";
require_once __DIR__ . "/RoboBaseTest.php";

use UliCMS\Helpers\TestHelper;

class RoboDatabaseTest extends RoboBaseTest {

    public function tearDown() {
        if ($this->shouldDropDbOnShutdown()) {
            $this->runRoboCommand(["database:reset"]);
        }
    }

    public function testShowException() {
        $actual = TestHelper::getOutput(function(){
            $actual = new BadMethodCallException("This is an error");
            $robo = new RoboTestFile();
            $robo->showException($actual);
        });
        
        $this->assertStringContainsString("This is an error", $actual);
    }

    public function testCreateAlreadyExists() {
        if (!$this->shouldDropDbOnShutdown()) {
            $this->markTestSkipped();
        }
        $actualDrop = $this->runRoboCommand(["database:drop"]);
        $this->assertStringContainsString('DROP SCHEMA IF EXISTS `', $actualDrop);

        new RoboFile();

        $actualCreate = $this->runRoboCommand(["database:create"]);
        $this->assertStringContainsString('CREATE DATABASE', $actualCreate);
    }

}
