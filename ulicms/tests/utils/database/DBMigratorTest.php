<?php

use UliCMS\Exceptions\NotImplementedException;

class DBMigratorTest extends \PHPUnit\Framework\TestCase {

    public function testCheckVarsWithComponentEmpty() {
        $migrator = new DBMigrator("", "");

        $this->expectException("Exception");
        $this->expectExceptionMessage("component is null or empty");
        $migrator->checkVars();
    }

    public function testCheckVarsWithFolderEmpty() {

        $migrator = new DBMigrator("gefüllt", "");

        $this->expectException("Exception");
        $this->expectExceptionMessage("folder is null or empty");
        $migrator->checkVars();
    }

    public function testCheckVarsWithNonExistingFolder() {
        $migrator = new DBMigrator("gefüllt", "dies_ist_ein_nichtordner");

        $this->expectException("Exception");
        $this->expectExceptionMessage("folder not found dies_ist_ein_nichtordner");
        $migrator->checkVars();
    }

    public function testDBMigratorThrowsNoError() {
        $migrator = new DBMigrator("core",
                Path::resolve("ULICMS_ROOT/lib/migrations/up")
        );

        $this->assertTrue($migrator->checkVars());
    }

    public function testMigrate() {
        throw new NotImplementedException();
    }

    public function testMigrateWithStop() {
        throw new NotImplementedException();
    }

    public function testRollback() {
        throw new NotImplementedException();
    }

    public function testRollbackWithStop() {
        throw new NotImplementedException();
    }

    public function testResetDBTrack() {
        throw new NotImplementedException();
    }

}
