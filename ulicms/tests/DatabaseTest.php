<?php

class DatabaseTest extends \PHPUnit\Framework\TestCase
{

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
        $this->assertTrue(version_compare($version, "5.5.3", '>='));
    }

    public function testAnyReturnsTrue()
    {
        $query = Database::query("select * from {prefix}settings where value <> ''", true);
        $this->assertTrue(Database::any($query));
    }

    public function testAnyReturnsFalse()
    {
        $query = Database::query("select * from {prefix}settings where value <> value", true);
        $this->assertFalse(Database::any($query));
    }

    public function testGetColumnNames()
    {
        $columns = Database::getColumnNames("users", true);
        $this->assertGreaterThanOrEqual(22, count($columns));
        $this->assertContains("username", $columns);
        $this->assertContains("about_me", $columns);
        $this->assertContains("homepage", $columns);
        $this->assertContains("password", $columns);
    }

    public function testGetNumRowsAny()
    {
        $query = Database::query("select * from {prefix}settings where name in ('homepage_title', 'frontpage', 'installed_at')", true);
        $this->assertEquals(3, Database::getNumRows($query));
    }

    public function testGetNumRowsZero()
    {
        $query = Database::query("select * from {prefix}settings where name in ('this_is_not_a_setting')", true);
        $this->assertEquals(0, Database::getNumRows($query));
    }

    public function testGetLastError()
    {
        // this sql fails always
        $query = Database::query("select devil from hell", true);
        $this->assertFalse($query);
        
        $error = Database::getLastError();
        
        $this->assertStringStartsWith("Table", $error);
        $this->assertStringEndsWith("doesn't exist", $error);
    }

    public function testError()
    {
        // this sql fails always
        $query = Database::query("select devil from hell", true);
        $this->assertFalse($query);
        
        $error = Database::error();
        
        $this->assertStringStartsWith("Table", $error);
        $this->assertStringEndsWith("doesn't exist", $error);
    }

    public function testGetError()
    {
        // this sql fails always
        $query = Database::query("select devil from hell", true);
        $this->assertFalse($query);
        
        $error = Database::getError();
        
        $this->assertStringStartsWith("Table", $error);
        $this->assertStringEndsWith("doesn't exist", $error);
    }

    public function testSelectAll()
    {
        $allSettings = Database::selectAll("settings");
        $this->assertTrue(Database::any($allSettings));
    }
    // TODO: implement tests for other Database functions
}