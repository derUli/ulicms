<?php

require_once __DIR__ . '/RoboTestFile.php';
require_once __DIR__ . '/RoboTestBase.php';

class RoboUmaskTest extends RoboTestBase {
    public function testThemesRemove(): void {
        $actual = $this->runRoboCommand(
            [
                'umask'
            ]
        );

        $this->assertEquals(4, strlen($actual));
        $this->assertIsNumeric($actual);
    }
}
