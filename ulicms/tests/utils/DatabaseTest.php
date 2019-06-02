<?php

class DatabaseTest extends \PHPUnit\Framework\TestCase {

    public function tearDown() {
        Database::dropTable("test_table");
        Settings::delete("foo");
        Settings::delete("foo2");
    }

    public function testIsConnectedReturnsTrue() {
        $this->assertTrue(Database::isConnected());
    }

    public function testIsConnectedReturnsFalse() {
        $oldConnection = Database::getConnection();

        Database::setConnection(null);

        $this->assertFalse(Database::isConnected());

        Database::setConnection($oldConnection);
    }

    public function testGetAllTables() {
        $tables = Database::getAllTables();
        $cfg = new CMSConfig();
        $prefix = $cfg->db_prefix;
        $this->assertGreaterThanOrEqual(21, count($tables));

        $this->assertContains("{$prefix}content", $tables);
        $this->assertContains("{$prefix}settings", $tables);
        $this->assertContains("{$prefix}users", $tables);
    }

    public function testGetServerVersion() {
        $version = Database::getServerVersion();
        $version = preg_replace('/[^0-9.].*/', '', $version);
        $this->assertTrue(version_compare($version, "5.5.3", '>='));
    }

    public function testAnyReturnsTrue() {
        $query = Database::query("select * from {prefix}settings where value <> ''", true);
        $this->assertTrue(Database::any($query));
    }

    public function testAnyReturnsFalse() {
        $query = Database::query("select * from {prefix}settings where value <> value", true);
        $this->assertFalse(Database::any($query));
    }

    public function testGetColumnNames() {
        $columns = Database::getColumnNames("users", true);
        $this->assertGreaterThanOrEqual(18, count($columns));
        $this->assertContains("username", $columns);
        $this->assertContains("about_me", $columns);
        $this->assertContains("homepage", $columns);
        $this->assertContains("password", $columns);
    }

    public function testGetNumRowsAny() {
        $query = Database::query("select * from {prefix}settings where name in ('homepage_title', 'frontpage', 'installed_at')", true);
        $this->assertEquals(3, Database::getNumRows($query));
    }

    public function testGetNumRowsZero() {
        $query = Database::query("select * from {prefix}settings where name in ('this_is_not_a_setting')", true);
        $this->assertEquals(0, Database::getNumRows($query));
    }

    public function testGetLastError() {
        // this sql fails always
        $query = Database::query("select devil from hell", true);
        $this->assertFalse($query);

        $error = Database::getLastError();

        $this->assertStringStartsWith("Table", $error);
        $this->assertStringEndsWith("doesn't exist", $error);
    }

    public function testError() {
        // this sql fails always
        $query = Database::query("select devil from hell", true);
        $this->assertFalse($query);

        $error = Database::error();

        $this->assertStringStartsWith("Table", $error);
        $this->assertStringEndsWith("doesn't exist", $error);
    }

    public function testGetError() {
        // this sql fails always
        $query = Database::query("select devil from hell", true);
        $this->assertFalse($query);

        $error = Database::getError();

        $this->assertStringStartsWith("Table", $error);
        $this->assertStringEndsWith("doesn't exist", $error);
    }

    public function testSelectAll() {
        $allSettings = Database::selectAll("settings");
        $this->assertTrue(Database::any($allSettings));
    }

    public function testDropTable() {
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

    public function testDropColumn() {
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

    public function testGetClientInfo() {
        $this->assertStringStartsWith("mysql", Database::getClientInfo());
    }

    public function testGetClientVersion() {
        // https://www.w3schools.com/php/func_mysqli_get_client_version.asp
        $this->assertGreaterThanOrEqual(50000, Database::getClientVersion());
    }

    public function testDeleteFrom() {
        Settings::set("foo", "bar");
        Database::deleteFrom("settings", "name = 'foo'");

        // clear settings cache
        SettingsCache::set("foo", null);

        $this->assertFalse(Settings::get("foo"));
    }

    public function testEscapeName() {
        $this->assertEquals("`alter`", Database::escapeName("alter"));
        $this->assertEquals("`JohnDoe`", Database::escapeName("'JohnDoe'"));
        $this->assertEquals("`JohnDoe`", Database::escapeName('"JohnDoe"'));
    }

    public function testGetConnectionReturnsMysqliObject() {
        $this->assertInstanceOf("mysqli", Database::getConnection());
    }

    public function testIsConnectedReturnsNull() {
        $oldConnection = Database::getConnection();

        Database::setConnection(null);

        $this->assertNull(Database::getConnection());

        Database::setConnection($oldConnection);
    }

    public function testGetLastInsertID() {
        Database::query("insert into {prefix}settings (name, value)
                         values
                         ('foo2', 'bar')", true);

        $lastInsertId = Database::getLastInsertID();
        $this->assertNotNull($lastInsertId);

        $query = Database::selectAll("settings", array(
                    "id"
                        ), "name = 'foo2'");
        $result = Database::fetchObject($query);
        $this->assertEquals($result->id, $lastInsertId);

        Settings::delete("foo2");
    }

    public function testGetInsertID() {
        Database::query("insert into {prefix}settings (name, value)
                         values
                         ('foo2', 'bar')", true);

        $lastInsertId = Database::getInsertID();
        $this->assertNotNull($lastInsertId);

        $query = Database::selectAll("settings", array(
                    "id"
                        ), "name = 'foo2'");
        $result = Database::fetchObject($query);
        $this->assertEquals($result->id, $lastInsertId);

        Settings::delete("foo2");
    }

    // TODO: implement tests for other Database functions
}
