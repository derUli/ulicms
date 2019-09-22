<?php

// test for legacy database methods
class DbFunctionsTest extends \PHPUnit\Framework\TestCase {

    public function testGetAllTables() {
        $tables = db_get_tables();
        $cfg = new CMSConfig();
        $prefix = $cfg->db_prefix;
        $this->assertGreaterThanOrEqual(21, count($tables));

        $this->assertContains("{$prefix}content", $tables);
        $this->assertContains("{$prefix}settings", $tables);
        $this->assertContains("{$prefix}users", $tables);
    }

    public function testTbName() {
        $tableName = tbname("random_table");

        $this->assertStringEndsWith("random_table", $tableName);
        $this->assertStringStartsNotWith("random_table", $tableName);
    }

    public function testGetServerVersion() {
        $version = db_get_server_info();
        $version = preg_replace('/[^0-9.].*/', '', $version);
        $this->assertTrue(version_compare($version, "5.5.3", '>='));
    }

    public function testGetClientInfo() {
        $this->assertStringStartsWith("mysql", db_get_client_info());
    }

}
