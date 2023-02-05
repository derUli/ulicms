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

    public function testGetClientInfo()
    {
        $this->assertStringStartsWith("mysql", db_get_client_info());
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

    public function testDbFetchAll()
    {
        $datasets = Database::selectAll("settings");

        $allSettings = db_fetch_all($datasets);
        foreach ($allSettings as $row) {
            $this->assertIsNumeric($row->id);
            $this->assertNotEmpty($row->name);
            $this->assertIsString($row->value);
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

    public function testDbEscapeName()
    {
        $this->assertEquals("`foobar`", db_name_escape('foobar'));
    }

    public function testDbAdffectedRows()
    {
        db_query("update " . tbname("content") . " set `views` = `views` + 1 where language = 'en'");
        $this->assertGreaterThan(1, db_affected_rows());
    }

    public function testDbLastInsertId()
    {
        $key = uniqid();
        Settings::set($key, "foobar");

        $this->assertGreaterThan(50, db_last_insert_id());
        Settings::delete($key);
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
                db_error()
            );
        }
    }

    public function testDbLastError()
    {
        try {
            Database::selectAll("gibts_nicht");
        } catch (SqlException $e) {
        } finally {
            $this->assertStringEndsWith(
                "gibts_nicht' doesn't exist",
                db_last_error()
            );
        }
    }

    public function testClose()
    {
        $this->assertTrue(Database::isConnected());

        db_close();
        $this->assertFalse(Database::isConnected());

        $this->reconnect(true);
        $this->assertTrue(Database::isConnected());

        db_close();

        $this->reconnect();
    }

    private function reconnect($db_strict_mode = null)
    {
        $config = new CMSConfig();
        $db_socket = isset($config->db_socket) ? $config->db_socket : ini_get("mysqli.default_socket");

        $db_port = isset($config->db_port) ? $config->db_port : ini_get("mysqli.default_port");

        if ($db_strict_mode === null) {
            $db_strict_mode = isset($config->db_strict_mode) ? boolval($config->db_strict_mode) : false;
        }

        db_connect($config->db_server, $config->db_user, $config->db_password, $db_port, $db_socket, $db_strict_mode);

        $this->assertFalse(schema_select("gibts_nicht"));

        $this->assertTrue(schema_select($config->db_database));
        $this->assertTrue(db_select_db($config->db_database));
        $this->assertTrue(schema_select($config->db_database));
        $this->assertTrue(db_select($config->db_database));
    }
}
