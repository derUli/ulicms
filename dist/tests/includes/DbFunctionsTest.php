<?php

use App\Exceptions\SqlException;

// test for legacy database methods
class DbFunctionsTest extends \PHPUnit\Framework\TestCase
{
    public function testTbName()
    {
        $tableName = tbname("random_table");

        $this->assertStringEndsWith("random_table", $tableName);
        $this->assertStringStartsNotWith("random_table", $tableName);
    }


    public function testDbFetchAssoc()
    {
        $datasets = Database::selectAll("settings");
        while ($row = db_fetch_assoc($datasets)) {
            $this->assertIsNumeric($row['id']);
            $this->assertNotEmpty($row['name']);
            $this->assertIsString($row["value"]);
        }
    }

    public function testDbFetchObject()
    {
        $datasets = Database::selectAll("settings");
        while ($row = db_fetch_object($datasets)) {
            $this->assertIsNumeric($row->id);
            $this->assertNotEmpty($row->name);
            $this->assertIsString($row->value);
        }
    }

    public function testDbNumRows()
    {
        $datasets = Database::selectAll("settings");
        $this->assertGreaterThanOrEqual(50, db_num_rows($datasets));
    }

    public function testDbQuery()
    {
        $query = db_query("select id from " . tbname("settings") . " where name = 'homepage_title' or name = 'site_slogan'");
        $this->assertEquals(2, db_num_rows($query));
    }

    public function testDbError()
    {
        try {
            Database::selectAll("gibts_nicht");
        } catch (SqlException $e) {
        } finally {
            $this->assertStringEndsWith(
                "gibts_nicht' doesn't exist",
                Database::getLastError()
            );
        }
    }
}
