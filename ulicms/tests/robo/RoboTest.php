<?php

require_once __DIR__ . "/RoboTestFile.php";
require_once __DIR__ . "/RoboBaseTest.php";

class RoboTest extends RoboBaseTest {

    protected function tearDown(): void {
        if ($this->shouldDropDbOnShutdown()) {
            $this->resetDb();
        }
    }

    public function testTestsRun() {
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
                "OK (5 tests, 42 assertions)",
                $actual);
    }

}
