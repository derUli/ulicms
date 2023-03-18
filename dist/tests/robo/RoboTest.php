<?php

require_once __DIR__ . "/RoboTestFile.php";
require_once __DIR__ . "/RoboTestBase.php";

class RoboTest extends RoboTestBase
{
    protected function tearDown(): void
    {
        if ($this->shouldDropDbOnShutdown()) {
            $this->resetDb();
        }
    }

    public function testTestsRun()
    {
        if (!$this->shouldDropDbOnShutdown()) {
            $this->markTestSkipped();
        }

        $cfg = new CMSConfig();
        Database::dropSchema($cfg->db_database);

        putenv("ULICMS_ENVIRONMENT=" . get_environment());

        $actual = $this->runRoboCommand(
            [
                "tests:run",
                "tests/environment/UliCMSVersionTest.php"
            ]
        );
        $this->assertStringContainsString(
            "OK (7 tests, 45 assertions)",
            $actual
        );
    }
}
