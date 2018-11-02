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
}