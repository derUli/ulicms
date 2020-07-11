<?php

require_once __DIR__ . "/RoboTestFile.php";
require_once __DIR__ . "/RoboBaseTest.php";

use UliCMS\Helpers\TestHelper;

class RoboDatabaseTest extends RoboBaseTest
{
    protected function tearDown(): void
    {
        if ($this->shouldDropDbOnShutdown()) {
            $this->runRoboCommand(["db:reset"]);
        }
    }

    public function testCreateAlreadyExists()
    {
        if (!$this->shouldDropDbOnShutdown()) {
            $this->markTestSkipped();
        }
        $actualDrop = $this->runRoboCommand(["db:drop"]);
        $this->assertStringContainsString('DROP SCHEMA IF EXISTS `', $actualDrop);

        new RoboFile();

        $actualCreate = $this->runRoboCommand(["db:create"]);
        $this->assertStringContainsString('CREATE DATABASE', $actualCreate);
    }
}
