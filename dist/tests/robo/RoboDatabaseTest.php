<?php

require_once __DIR__ . '/RoboTestFile.php';
require_once __DIR__ . '/RoboTestBase.php';

class RoboDatabaseTest extends RoboTestBase {
    protected function tearDown(): void {
        if ($this->shouldDropDbOnShutdown()) {
            $this->runRoboCommand(['db:reset']);
        }
    }

    public function testCreateAlreadyExists(): void {
        if (! $this->shouldDropDbOnShutdown()) {
            $this->markTestSkipped();
        }
        $actualDrop = $this->runRoboCommand(['db:drop']);
        $this->assertStringContainsString('DROP SCHEMA IF EXISTS `', $actualDrop);

        $actualCreate = $this->runRoboCommand(['db:create']);
        $this->assertStringContainsString('CREATE DATABASE', $actualCreate);
    }
}
