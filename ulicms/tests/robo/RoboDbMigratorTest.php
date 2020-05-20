<?php

require_once __DIR__ . "/RoboTestFile.php";
require_once __DIR__ . "/RoboBaseTest.php";

class RoboDbMigratorTest extends RoboBaseTest {

    public function testDbMigratorList() {
        $output = $this->runRoboCommand(["dbmigrator:list", "core"]);
        $this->assertStringContainsString("021.sql", $output);
        $this->assertStringContainsString("001.sql", $output);
        $this->assertGreaterThanOrEqual(21, substr_count($output, "core"));
    }

}
