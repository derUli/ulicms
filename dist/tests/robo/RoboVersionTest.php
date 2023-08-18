<?php

require_once __DIR__ . '/RoboTestFile.php';
require_once __DIR__ . '/RoboTestBase.php';

class RoboVersionTest extends RoboTestBase {
    public function testVersion(): void {
        $output = $this->runRoboCommand(['version']);

        $this->assertStringStartsWith('2024', $output);
        $this->assertStringContainsString('.', $output);
    }
}
