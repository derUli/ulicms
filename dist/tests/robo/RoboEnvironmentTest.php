<?php

require_once __DIR__ . '/RoboTestFile.php';
require_once __DIR__ . '/RoboTestBase.php';

class RoboEnvironmentTest extends RoboTestBase {
    public function testVersion(): void {
        $output = $this->runRoboCommand(['environment']);
        $this->assertEquals('test', $output);
    }
}
