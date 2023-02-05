<?php

use UliCMS\Exceptions\SqlException;

// test for legacy database methods
class DbFunctionsTest extends \PHPUnit\Framework\TestCase
{
    public function testGetAllTables()
    {
        $tables = db_get_tables();
        $cfg = new CMSConfig();
        $prefix = $cfg->db_prefix;
        $this->assertGreaterThanOrEqual(21, count($tables));

        $this->assertContains("{$prefix}content", $tables);
        $this->assertContains("{$prefix}settings", $tables);
        $this->assertContains("{$prefix}users", $tables);
    }

    public function testTbName()
    {
        $tableName = tbname("random_table");

        $this->assertStringEndsWith("random_table", $tableName);
        $this->assertStringStartsNotWith("random_table", $tableName);
    }

    public function testDbNumFields()
    {
        Database::selectAll("settings");
        $this->assertEquals(3, db_num_fields());
    }

    public function testDbFetchRow()
    {
        $datasets = Database::selectAll("settings");
        while ($row = db_fetch_row($datasets)) {
            $this->assertIsNumeric($row[0]);
            $this->assertNotEmpty($row[1]);
            $this->assertIsString($row[2]);
        }
    }

    public function testDbFetchAssoc()
    {
        $datasets = Database::selectAll("settings");
        while ($row = db_fetch_assoc($datasets)) {
            $this->assertIsNumeric($row["id"]);
            $this->assertNotEmpty($row["name"]);
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

    public function testDbFetchArray()
    {
        $datasets = Database::selectAll("settings");
        while ($row = db_fetch_array($datasets)) {
            $this->assertIsNumeric($row["id"]);
            $this->assertNotEmpty($row["name"]);
            $this->assertIsString($row["value"]);

            $this->assertIsNumeric($row[0]);
            $this->assertNotEmpty($row[1]);
            $this->assertIsString($row[2]);
        }
    }

    public function testDbFetchField()
    {
        $cfg = new CMSConfig();

        $datasets = Database::selectAll("settings");
        while ($row = db_fetch_field($datasets)) {
            $this->assertNotNull($row->name);
            $this->assertStringEndsWith("settings", $row->table);
            $this->assertStringEndsWith("settings", $row->orgtable);
            $this->assertEquals($cfg->db_database, $row->db);
        }
    }

    public function testDbNumRows()
    {
        $datasets = Database::selectAll("settings");
        $this->assertGreaterThanOrEqual(50, db_num_rows($datasets));
        ;
    }

    public function testDbRealEscapeString()
    {
        $this->assertEquals(
            "\\'foo\\'",
            db_real_escape_string("'foo'")
        );
    }

    public function testDbQuery()
    {
        $query = db_query("select * from " . tbname("settings") . " where name = 'homepage_title' or name = 'site_slogan'");
        $this->assertEquals(2, db_num_rows($query));
    }

    public function testDbInsertId()
    {
        $key = uniqid();
        Settings::set($key, "foobar");

        $this->assertGreaterThan(50, db_insert_id());
        Settings::delete($key);
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
