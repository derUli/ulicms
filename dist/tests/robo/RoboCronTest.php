<?php

require_once __DIR__ . '/RoboTestFile.php';
require_once __DIR__ . '/RoboTestBase.php';

class RoboCronTest extends RoboTestBase
{
    public function testThemesRemove()
    {
        $actual = $this->runRoboCommand(
            [
                'cron'
            ]
        );
        $this->assertStringContainsString('Finished cron at', $actual);
    }
}
