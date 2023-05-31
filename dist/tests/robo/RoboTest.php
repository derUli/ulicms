<?php

require_once __DIR__ . '/RoboTestFile.php';
require_once __DIR__ . '/RoboTestBase.php';

class RoboTest extends RoboTestBase {
    protected function tearDown(): void {
        if ($this->shouldDropDbOnShutdown()) {
            $this->resetDb();
        }
    }

    /**
     * @medium
     */
    public function testTestsRun(): void {
        if (! $this->shouldDropDbOnShutdown()) {
            $this->markTestSkipped();
        }

        Database::dropSchema($_ENV['DB_DATABASE']);

        putenv('APP_ENV=' . get_environment());

        $actual = $this->runRoboCommand(
            [
                'tests:run',
                'tests/Unit/App/UliCMS/UliCMSVersionTest.php'
            ]
        );

        $this->assertStringContainsString(
            'OK (7 tests, ',
            $actual
        );
    }
}
