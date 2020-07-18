<?php

require_once __DIR__ . "/RoboTestFile.php";
require_once __DIR__ . "/RoboBaseTest.php";

class RoboTruncateTest extends RoboBaseTest
{
    public function testTruncateHistory()
    {
        $this->runRoboCommand(["truncate:history"]);
        $query = Database::selectAll("history", ["id"]);
        $this->assertEquals(0, mysqli_num_rows($query));
    }

    public function testTruncateMails()
    {
        $this->runRoboCommand(["truncate:mails"]);
        $query = Database::selectAll("mails", ["id"]);
        $this->assertEquals(0, mysqli_num_rows($query));
    }
}
