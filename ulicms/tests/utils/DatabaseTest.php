<?php

use UliCMS\Exceptions\NotImplementedException;

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
        $result = Database::query("select * from {prefix}settings where value <> ''", true);
        $this->assertTrue(Database::any($result));
    }

    public function testAnyReturnsFalse() {
        $result = Database::query("select * from {prefix}settings where value <> value", true);
        $this->assertFalse(Database::any($result));
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
        $result = Database::query("select * from {prefix}settings where name in ('homepage_title', 'frontpage', 'installed_at')", true);
        $this->assertEquals(3, Database::getNumRows($result));
    }

    public function testGetNumRowsZero() {
        $result = Database::query("select * from {prefix}settings where name in ('this_is_not_a_setting')", true);
        $this->assertEquals(0, Database::getNumRows($result));
    }

    public function testGetLastError() {
        // this sql fails always
        $result = Database::query("select devil from hell", true);
        $this->assertFalse($result);

        $error = Database::getLastError();

        $this->assertStringStartsWith("Table", $error);
        $this->assertStringEndsWith("doesn't exist", $error);
    }

    public function testError() {
        // this sql fails always
        $result = Database::query("select devil from hell", true);
        $this->assertFalse($result);

        $error = Database::error();

        $this->assertStringStartsWith("Table", $error);
        $this->assertStringEndsWith("doesn't exist", $error);
    }

    public function testGetError() {
        // this sql fails always
        $result = Database::query("select devil from hell", true);
        $this->assertFalse($result);

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

        $this->assertNull(Settings::get("foo"));
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

        $result = Database::selectAll("settings", array(
                    "id"
                        ), "name = 'foo2'");
        $dataset = Database::fetchObject($result);
        $this->assertEquals($dataset->id, $lastInsertId);

        Settings::delete("foo2");
    }

    public function testGetInsertID() {
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

    public function testGetNumFieldCount() {
        Database::selectAll("users", ["lastname", "firstname", "email"]);
        $this->assertEquals(3, Database::getNumFieldCount());

        Database::selectAll("content", ["slug", "title"]);
        $this->assertEquals(2, Database::getNumFieldCount());
    }

    public function testGetAffectedRows() {
        for ($i = 1; $i <= 13; $i++) {
            Settings::set("test_setting_{$i}", 1);
        }
        Database::deleteFrom("settings", "name like 'test_setting_%'");
        $this->assertEquals(13, Database::getAffectedRows());
    }

    public function testSelectMinReturnsZero() {
        $this->assertEquals(0, Database::selectMin("settings", "id", "1 = 0"));
    }

    public function testSelectMinReturnsMin() {
        $min = Database::selectMin("settings", "id");
        $max = Database::selectMax("settings", "id");

        $this->assertIsInt($min);
        $this->assertGreaterThanOrEqual(1, $min);
        $this->assertLessThan($max, $min);
    }

    public function testSelectMaxReturnsZero() {
        $this->assertEquals(0, Database::selectMax("settings", "id", "1 = 0"));
    }

    public function testSelectMaxReturnsMax() {
        $min = Database::selectMin("settings", "id");
        $max = Database::selectMax("settings", "id");
        $this->assertIsInt($max);
        $this->assertGreaterThan($min, $max);
    }

    public function testSelectAvgReturnsZero() {
        $this->assertEquals(0, Database::selectAvg("settings", "id", "1 = 0"));
    }

    public function testSelectAvgReturnsAvg() {
        $min = Database::selectMin("settings", "id");
        $max = Database::selectMax("settings", "id");
        $avg = Database::selectAvg("settings", "id");
        $this->assertIsFloat($avg);

        $this->assertGreaterThan($min, $avg);
        $this->assertLessThan($max, $avg);
    }

    public function testFetchAll() {
        $result = Database::query(
                        "select * from {prefix}settings where
        name in
        ('default_font', 'frontpage', 'homepage_title')
        order by name", true);

        $datasets = Database::fetchAll($result);
        $this->assertCount(3, $datasets);
        $this->assertEquals('default_font', $datasets[0]->name);
        $this->assertEquals('frontpage', $datasets[1]->name);
        $this->assertEquals('homepage_title', $datasets[2]->name);
        foreach ($datasets as $dataset) {
            $this->assertNotEmpty($dataset->value);
        }
    }

    // TODO: implement tests for other Database functions
}
