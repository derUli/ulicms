<?php

use UliCMS\Exceptions\NotImplementedException;

class DBMigratorTest extends \PHPUnit\Framework\TestCase {

    const DB_MIGRATOR_UP_DIR = "ULICMS_ROOT/tests/fixtures/migrations/up";
    const DB_MIGRATOR_DOWN_DIR = "ULICMS_ROOT/tests/fixtures/migrations/down";

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

        for ($i = 1; $i <= 3; $i++) {
            $sql = "INSERT INTO {prefix}dbtrack (component, name) "
                    . "values (?,?)";
            $args = ["dbmigrator_test", uniqid()];
            Database::pQuery($sql, $args, true);
        }

        $this->assertTrue(
                Database::any(
                        Database::selectAll("dbtrack", ["id"], "component = 'dbmigrator_test'"
                        )
                )
        );

        $dbmigrator = new DBMigrator("dbmigrator_test", self::DB_MIGRATOR_UP_DIR);
        $dbmigrator->resetDBTrack("dbmigrator_test");
        $this->assertFalse(
                Database::any(
                        Database::selectAll("dbtrack",
                                ["id"],
                                "component = 'dbmigrator_test'"
                        )
                )
        );
    }

}
