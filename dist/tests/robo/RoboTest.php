<?php

require_once __DIR__ . '/RoboTestFile.php';
require_once __DIR__ . '/RoboTestBase.php';

class RoboTest extends RoboTestBase
{
    protected function tearDown(): void
    {
        if ($this->shouldDropDbOnShutdown()) {
            $this->resetDb();
        }
    }

    /**
     * @medium
     */
    public function testTestsRun()
    {
        if (! $this->shouldDropDbOnShutdown()) {
            $this->markTestSkipped();
        }

        $cfg = new CMSConfig();
        Database::dropSchema($cfg->db_database);

        putenv('APP_ENV=' . get_environment());

        $actual = $this->runRoboCommand(
            [
                'tests:run',
                'tests/Unit/App/Backend/UliCMSVersionTest.php'
            ]
        );
        $this->assertStringContainsString(
            'OK (7 tests, 47 assertions)',
            $actual
        );
    }
}
