<?php

use UliCMS\Exceptions\SqlException;

class DatabaseTest extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        Database::setEchoQueries(false);
        Database::dropTable("test_table");
        Settings::delete("foo");
        Settings::delete("foo2");

        $configuration = new CMSConfig();
        Database::select($configuration->db_database);
    }

    public function testIsConnectedReturnsTrue()
    {
        $this->assertTrue(Database::isConnected());
    }

    public function testIsSchemaSelectedReturnsTrue()
    {
        $this->assertTrue(Database::isSchemaSelected());
    }

    public function testIsSchemaSelectedReturnsFalse()
    {
        Database::select("nothing");
        $this->assertFalse(Database::isSchemaSelected());
    }

    public function testIsConnectedReturnsFalse()
    {
        $oldConnection = Database::getConnection();

        Database::setConnection(null);

        $this->assertFalse(Database::isConnected());

        Database::setConnection($oldConnection);
    }

    public function testGetAllTables()
    {
        $tables = Database::getAllTables();
        $cfg = new CMSConfig();
        $prefix = $cfg->db_prefix;
        $this->assertGreaterThanOrEqual(21, count($tables));

        $this->assertContains("{$prefix}content", $tables);
        $this->assertContains("{$prefix}settings", $tables);
        $this->assertContains("{$prefix}users", $tables);
    }

    public function testGetServerVersion()
    {
        $version = Database::getServerVersion();
        $version = preg_replace('/[^0-9.].*/', '', $version);
        $this->assertTrue(\UliCMS\Utils\VersionComparison\compare($version, "5.5.3", '>='));
    }

    public function testAnyReturnsTrue()
    {
        $result = Database::query("select * from {prefix}settings where value <> ''", true);
        $this->assertTrue(Database::any($result));
    }

    public function testAnyReturnsFalse()
    {
        $result = Database::query("select * from {prefix}settings where value <> value", true);
        $this->assertFalse(Database::any($result));
    }

    public function testGetColumnNames()
    {
        $columns = Database::getColumnNames("users", true);
        $this->assertGreaterThanOrEqual(18, count($columns));
        $this->assertContains("username", $columns);
        $this->assertContains("about_me", $columns);
        $this->assertContains("homepage", $columns);
        $this->assertContains("password", $columns);
    }

    public function testGetNumRowsAny()
    {
        $result = Database::query("select * from {prefix}settings where name in ('homepage_title', 'frontpage', 'installed_at')", true);
        $this->assertEquals(3, Database::getNumRows($result));
    }

    public function testGetNumRowsZero()
    {
        $result = Database::query("select * from {prefix}settings where name in ('this_is_not_a_setting')", true);
        $this->assertEquals(0, Database::getNumRows($result));
    }

    public function testGetLastError()
    {
        $this->expectException(SqlException::class);
        // this sql fails always
        $result = Database::query("select devil from hell", true);
        $this->assertFalse($result);

        $error = Database::getLastError();

        $this->assertStringStartsWith("Table", $error);
        $this->assertStringEndsWith("doesn't exist", $error);
    }

    public function testError()
    {
        // this sql fails always
        try {
            $result = Database::query("select devil from hell", true);
            $this->assertFalse($result);
        } catch (Exception $e) {
            $error = Database::error();

            $this->assertStringStartsWith("Table", $error);
            $this->assertStringEndsWith("doesn't exist", $error);
        }
    }

    protected function getSQLLogger(): Logger
    {
        $path = Path::resolve("ULICMS_LOG/sql_exception");
        return new Logger($path);
    }

    public function testGetError()
    {
        LoggerRegistry::register("sql_log", $this->getSQLLogger());
        try {
            // this sql fails always
            $result = Database::query("select devil from hell", true);
            $this->assertFalse($result);
        } catch (SqlException $e) {
            $error = Database::getError();

            $this->assertStringStartsWith("Table", $error);
            $this->assertStringEndsWith("doesn't exist", $error);
        }
        LoggerRegistry::unregister("sql_log");
    }

    public function testSelectAll()
    {
        $allSettings = Database::selectAll("settings");
        $this->assertTrue(Database::any($allSettings));
    }

    public function testDropTable()
    {
        Database::query("CREATE TABLE {prefix}test_table (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        firstname VARCHAR(30) NOT NULL,
                        lastname VARCHAR(30) NOT NULL,
                        email VARCHAR(50),
                        reg_date TIMESTAMP)", true);

        $cfg = new CMSConfig();
        $prefix = $cfg->db_prefix;

        $this->assertContains("{$prefix}test_table", Database::getAllTables());
        Database::dropTable("test_table");
        $this->assertNotContains("{$prefix}test_table", Database::getAllTables());
    }

    public function testDropColumn()
    {
        Database::query("CREATE TABLE {prefix}test_table (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        firstname VARCHAR(30) NOT NULL,
                        lastname VARCHAR(30) NOT NULL,
                        email VARCHAR(50),
                        reg_date TIMESTAMP)", true);

        $this->assertContains("reg_date", Database::getColumnNames("test_table"));

        Database::dropColumn("test_table", "reg_date");
        $this->assertNotContains("reg_date", Database::getColumnNames("test_table"));

        Database::dropTable("test_table");
    }

    public function testGetClientInfo()
    {
        $this->assertStringStartsWith("mysql", Database::getClientInfo());
    }

    public function testGetClientVersion()
    {
        // https://www.w3schools.com/php/func_mysqli_get_client_version.asp
        $this->assertGreaterThanOrEqual(50000, Database::getClientVersion());
    }


    public function testEscapeName()
    {
        $this->assertEquals("`alter`", Database::escapeName("alter"));
        $this->assertEquals("`JohnDoe`", Database::escapeName("'JohnDoe'"));
        $this->assertEquals("`JohnDoe`", Database::escapeName('"JohnDoe"'));
    }

    public function testGetConnectionReturnsMysqliObject()
    {
        $this->assertInstanceOf("mysqli", Database::getConnection());
    }

    public function testIsConnectedReturnsNull()
    {
        $oldConnection = Database::getConnection();

        Database::setConnection(null);

        $this->assertNull(Database::getConnection());

        Database::setConnection($oldConnection);
    }

    public function testGetLastInsertID()
    {
        Database::query("insert into {prefix}settings (name, value)
                         values
                         ('foo2', 'bar')", true);

        $lastInsertId = Database::getLastInsertID();
        $this->assertNotNull($lastInsertId);

        $result = Database::selectAll("settings", array(
                    "id"
                        ), "name = 'foo2'");
        $dataset = Database::fetchObject($result);
        $this->assertEquals($dataset->id, $lastInsertId);

        Settings::delete("foo2");
    }

    public function testGetInsertID()
    {
        Database::query("insert into {prefix}settings (name, value)
                         values
                         ('foo2', 'bar')", true);

        $lastInsertId = Database::getInsertID();
        $this->assertNotNull($lastInsertId);

        $result = Database::selectAll("settings", array(
                    "id"
                        ), "name = 'foo2'");
        $dataset = Database::fetchObject($result);
        $this->assertEquals($dataset->id, $lastInsertId);

        Settings::delete("foo2");
    }

    public function testGetNumFieldCount()
    {
        Database::selectAll("users", ["lastname", "firstname", "email"]);
        $this->assertEquals(3, Database::getNumFieldCount());

        Database::selectAll("content", ["slug", "title"]);
        $this->assertEquals(2, Database::getNumFieldCount());
    }

    public function testGetAffectedRows()
    {
        for ($i = 1; $i <= 13; $i++) {
            Settings::set("test_setting_{$i}", 1);
        }
        Database::deleteFrom("settings", "name like 'test_setting_%'");
        $this->assertEquals(13, Database::getAffectedRows());
    }

    public function testSelectMinReturnsZero()
    {
        $this->assertEquals(0, Database::selectMin("settings", "id", "1 = 0"));
    }

    public function testSelectMinReturnsMin()
    {
        $min = Database::selectMin("settings", "id");
        $max = Database::selectMax("settings", "id");

        $this->assertIsInt($min);
        $this->assertGreaterThanOrEqual(1, $min);
        $this->assertLessThan($max, $min);
    }

    public function testSelectMaxReturnsZero()
    {
        $this->assertEquals(0, Database::selectMax("settings", "id", "1 = 0"));
    }

    public function testSelectMaxReturnsMax()
    {
        $min = Database::selectMin("settings", "id");
        $max = Database::selectMax("settings", "id");
        $this->assertIsInt($max);
        $this->assertGreaterThan($min, $max);
    }

    public function testSelectAvgReturnsZero()
    {
        $this->assertEquals(0, Database::selectAvg("settings", "id", "1 = 0"));
    }

    public function testSelectAvgReturnsAvg()
    {
        $min = Database::selectMin("settings", "id");
        $max = Database::selectMax("settings", "id");
        $avg = Database::selectAvg("settings", "id");
        $this->assertIsFloat($avg);

        $this->assertGreaterThan($min, $avg);
        $this->assertLessThan($max, $avg);
    }

    public function testFetchAll()
    {
        $result = Database::query(
            "select * from {prefix}settings where
        name in
        ('default_font', 'frontpage', 'homepage_title')
        order by name",
            true
        );

        $datasets = Database::fetchAll($result);
        $this->assertCount(3, $datasets);
        $this->assertEquals('default_font', $datasets[0]->name);
        $this->assertEquals('frontpage', $datasets[1]->name);
        $this->assertEquals('homepage_title', $datasets[2]->name);
        foreach ($datasets as $dataset) {
            $this->assertNotEmpty($dataset->value);
        }
    }

    public function testGetSqlStrictModeFlags()
    {
        $this->assertCount(7, Database::getSqlStrictModeFlags());
        foreach (Database::getSqlStrictModeFlags() as $flag) {
            $this->assertIsString($flag);
            $this->assertNotEmpty($flag);

            // string must be Uppercase
            $this->assertEquals(strtoupper($flag), $flag);
        }
    }

    public function testTableExistsReturnsTrue()
    {
        $this->assertTrue(Database::tableExists("content"));
        $this->assertTrue(Database::tableExists("settings"), true);
        $this->assertTrue(Database::tableExists(tbname("content"), false));
    }

    public function testTableExistsReturnsFalse()
    {
        $this->assertFalse(Database::tableExists("gibts_echt_nicht"));
        $this->assertFalse(Database::tableExists("content", false));
    }

    public function testFetchArray()
    {
        $query = Database::selectAll("settings");

        while ($row = Database::fetchArray($query)) {
            $this->assertCount(6, $row);
            $this->assertNotEmpty($row["name"]);
        }
    }

    public function testFetchRow()
    {
        $query = Database::selectAll("settings");

        while ($row = Database::fetchRow($query)) {
            $this->assertCount(3, $row);
            $this->assertIsNumeric($row[0]);
            $this->assertNotEmpty($row[1]);
        }
    }

    public function testFetchSingleOrDefaultReturnsObject()
    {
        $query = Database::selectAll("settings", [], " 1 = 1 limit 1");

        $default = new stdClass();
        $default->name = "default value";

        $row = Database::fetchSingleOrDefault($query, $default);
        $this->assertIsObject($row);
        $this->assertIsNumeric($row->id);
        $this->assertNotEmpty($row->name);
    }

    public function testFetchFirstReturnsNull()
    {
        $query = Database::selectAll("settings", [], "1 = 0");

        $row = Database::fetchFirst($query);
        $this->assertNull($row);
    }

    public function testFetchFirstReturnsObject()
    {
        $query = Database::selectAll("settings", [], "1=1");

        $row = Database::fetchFirst($query);
        $this->assertIsObject($row);
        $this->assertIsNumeric($row->id);
        $this->assertNotEmpty($row->name);
    }

    public function testFetchSingleOrDefaultThrowsException()
    {
        $query = Database::selectAll("settings", []);

        $default = new stdClass();
        $default->name = "default value";

        $this->expectException(RangeException::class);

        Database::fetchSingleOrDefault($query, $default);
    }

    public function testFetchSingleOrDefaultReturnsDefault()
    {
        $query = Database::selectAll("settings", [], "1 = 0");

        $default = new stdClass();
        $default->name = "default value";

        $row = Database::fetchSingleOrDefault($query, $default);

        $this->assertIsObject($row);
        $this->assertEquals("default value", $row->name);
    }

    public function testEscapeValueWithFloat()
    {
        $this->assertEquals(2.99, Database::escapevalue(2.99));
    }

    public function testEscapeValueWithTypeInt()
    {
        $this->assertEquals(2, Database::escapevalue(2.99, DB_TYPE_INT));
    }

    public function testEscapeValueWithTypeFloat()
    {
        $this->assertEquals(2.0, Database::escapevalue(2, DB_TYPE_FLOAT));
    }

    public function testEscapeValueWithTypeString()
    {
        $this->assertEquals("123", Database::escapevalue(123, DB_TYPE_STRING));
    }

    public function testEscapeValueWithTypeBool()
    {
        $this->assertEquals(0, Database::escapevalue(false, DB_TYPE_BOOL));
        $this->assertEquals(1, Database::escapevalue(true, DB_TYPE_BOOL));
    }

    public function testEscapeValueWithTypeOther()
    {
        $this->assertInstanceOf(Page::class, Database::escapevalue(new Page(), PHP_INT_MAX));
    }

    public function testfetchFirstOrDefaultReturnsFirst()
    {
        $query = Database::selectAll("settings", []);

        $default = new stdClass();
        $default->name = "default value";

        $row = Database::fetchFirstOrDefault($query, $default);

        $this->assertIsNumeric($row->id);
        $this->assertNotEmpty($row->name);
    }

    public function testfetchFirstOrDefaultReturnsDefault()
    {
        $query = Database::selectAll("settings", [], "1 = 0");

        $default = new stdClass();
        $default->name = "default value";

        $row = Database::fetchFirstOrDefault($query, $default);

        $this->assertIsObject($row);
        $this->assertEquals("default value", $row->name);
    }

    public function testFetchSingleReturnsNull()
    {
        $query = Database::selectAll("settings", [], "1 = 0");

        $row = Database::fetchSingle($query);
        $this->assertNull($row);
    }

    public function testFetchSingleThrowsException()
    {
        $query = Database::selectAll("settings");

        $this->expectException(RangeException::class);
        Database::fetchSingle($query);
    }

    public function testFetchSingleReturnsObject()
    {
        $query = Database::selectAll("settings", [], "1=1 limit 1");

        $row = Database::fetchSingle($query);
        $this->assertIsObject($row);
        $this->assertIsNumeric($row->id);
        $this->assertNotEmpty($row->name);
    }

    public function hasMoreResultsReturnsFalse()
    {
        $this->assertFalse(Database::hasMoreResults());
    }

    public function testCreateSelectAndDropSchema()
    {
        $schema = "tmp_database_" . uniqid();
        $this->assertTrue(Database::createSchema($schema));
        $this->assertTrue(Database::select($schema));

        $configuration = new CMSConfig();
        $this->assertTrue(
            Database::select($configuration->db_database)
        );

        $this->assertTrue(Database::dropSchema($schema));
    }

    public function testEchoQueriesOutputsSQL()
    {
        Database::setEchoQueries(true);
        ob_start();
        Database::query("select 'foo' as bar");
        $this->assertEquals("select 'foo' as bar\n", ob_get_clean());
    }

    public function testEchoQueriesOutputsNothing()
    {
        Database::setEchoQueries(false);
        ob_start();
        Database::query("select 'foo' as bar");
        $this->assertEmpty(ob_get_clean());
    }

    public function testClose()
    {
        $this->assertTrue(Database::isConnected());

        Database::close();
        $this->assertFalse(Database::isConnected());

        $this->reconnect(true);
        $this->assertTrue(Database::isConnected());

        Database::close();

        $this->reconnect();
    }

    public function testConnectFails()
    {
        Database::close();

        $config = new CMSConfig();
        $db_socket = isset($config->db_socket) ? $config->db_socket : ini_get("mysqli.default_socket");
        $db_port = isset($config->db_port) ? $config->db_port : ini_get("mysqli.default_port");

        $db_strict_mode = isset($config->db_strict_mode) ? boolval($config->db_strict_mode) : false;

        @$connect = Database::connect($config->db_server, $config->db_user, "invalid_password", $db_port, $db_socket, $db_strict_mode);
        $this->assertNull($connect);


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

        Database::connect($config->db_server, $config->db_user, $config->db_password, $db_port, $db_socket, $db_strict_mode);

        Database::select($config->db_database);
    }

    public function testMultiQuery()
    {
        Database::setEchoQueries(true);
        ob_start();

        LoggerRegistry::register("sql_log", $this->getSQLLogger());

        Database::multiQuery(
            "select * from {prefix}settings; select 'foo' as bar;"
                . "show tables;",
            true
        );

        $queries = 0;
        while (Database::hasMoreResults()) {
            Database::loadNextResult();
            $result = Database::storeResult();
            $this->assertInstanceOf(mysqli_result::class, $result);
            $this->assertGreaterThanOrEqual(1, Database::getNumRows($result));
            $queries++;
        }
        $this->assertEquals(3, $queries);
        LoggerRegistry::unregister("sql_log");
        ob_end_clean();
    }
}
