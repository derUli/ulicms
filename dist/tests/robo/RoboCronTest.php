<?php

require_once __DIR__ . '/RoboTestFile.php';
require_once __DIR__ . '/RoboTestBase.php';

class RoboCronTest extends RoboTestBase {
    public function testThemesRemove(): void {
        $actual = $this->runRoboCommand(
            [
                'cron'
            ]
        );

        $this->assertEmpty($actual);
    }
}
