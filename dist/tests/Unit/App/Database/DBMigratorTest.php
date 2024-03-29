<?php

use App\Database\DBMigrator;
use App\Exceptions\SqlException;

class DBMigratorTest extends \PHPUnit\Framework\TestCase {
    public const DB_MIGRATOR_UP_DIR = 'ULICMS_ROOT/tests/fixtures/migrations/up';

    public const DB_MIGRATOR_DOWN_DIR = 'ULICMS_ROOT/tests/fixtures/migrations/down';

    public const DB_MIGRATOR_FAILED_UP_DIR = 'ULICMS_ROOT/tests/fixtures/failed_migrations/up';

    public const DB_MIGRATOR_FAILED_DOWN_DIR = 'ULICMS_ROOT/tests/fixtures/failed_migrations/down';

    protected function tearDown(): void {
        Database::dropTable('employees');

        $dbmigrator = new DBMigrator('dbmigrator_test', self::DB_MIGRATOR_UP_DIR);
        $dbmigrator->resetDBTrack('dbmigrator_test');
    }

    public function testCheckVarsWithComponentEmpty(): void {
        $migrator = new DBMigrator('', '');

        $this->expectException('Exception');
        $this->expectExceptionMessage('component is null or empty');
        $migrator->checkVars();
    }

    public function testCheckVarsWithFolderEmpty(): void {
        $migrator = new DBMigrator('gefüllt', '');

        $this->expectException('Exception');
        $this->expectExceptionMessage('folder is null or empty');
        $migrator->checkVars();
    }

    public function testCheckVarsWithNonExistingFolder(): void {
        $migrator = new DBMigrator('gefüllt', 'dies_ist_ein_nichtordner');

        $this->expectException('Exception');
        $this->expectExceptionMessage('folder not found dies_ist_ein_nichtordner');
        $migrator->checkVars();
    }

    public function testDBMigratorThrowsNoError(): void {
        $migrator = new DBMigrator(
            'core',
            \App\Utils\Path::resolve('ULICMS_ROOT/lib/migrations/up')
        );

        $this->assertTrue($migrator->checkVars());
    }

    public function testResetDBTrack(): void {
        for ($i = 1; $i <= 3; $i++) {
            $sql = 'INSERT INTO {prefix}dbtrack (component, name) '
                    . 'values (?,?)';
            $args = ['dbmigrator_test', uniqid()];
            Database::pQuery($sql, $args, true);
        }

        $this->assertTrue(
            Database::any(
                Database::selectAll(
                    'dbtrack',
                    ['id'],
                    "component = 'dbmigrator_test'"
                )
            )
        );

        $dbmigrator = new DBMigrator('dbmigrator_test', self::DB_MIGRATOR_UP_DIR);
        $dbmigrator->resetDBTrack('dbmigrator_test');

        $this->assertFalse(
            Database::any(
                Database::selectAll(
                    'dbtrack',
                    ['id'],
                    "component = 'dbmigrator_test'"
                )
            )
        );
    }

    public function testMigrateWithStop(): void {
        $dbmigrator = new DBMigrator(
            'dbmigrator_test',
            \App\Utils\Path::resolve(self::DB_MIGRATOR_UP_DIR)
        );
        $dbmigrator->migrate('001.sql');

        $columns = Database::getColumnNames('employees');
        $this->assertCount(5, $columns);
        $this->assertNotContains('email', $columns);
    }

    public function testMigrate(): void {
        $dbmigrator = new DBMigrator(
            'dbmigrator_test',
            \App\Utils\Path::resolve(self::DB_MIGRATOR_UP_DIR)
        );
        $dbmigrator->migrate();

        $columns = Database::getColumnNames('employees');
        $this->assertCount(6, $columns);
        $this->assertContains('email', $columns);
    }

    public function testRollback(): void {
        $dbmigrator = new DBMigrator(
            'dbmigrator_test',
            \App\Utils\Path::resolve(self::DB_MIGRATOR_UP_DIR)
        );
        $dbmigrator->migrate();

        $this->assertTrue(Database::tableExists('employees'));

        $dbmigrator = new DBMigrator(
            'dbmigrator_test',
            \App\Utils\Path::resolve(self::DB_MIGRATOR_DOWN_DIR)
        );
        $dbmigrator->rollback();

        $this->assertFalse(Database::tableExists('employees'));
    }

    public function testRollbackWithStop(): void {
        $dbmigrator = new DBMigrator(
            'dbmigrator_test',
            \App\Utils\Path::resolve(self::DB_MIGRATOR_UP_DIR)
        );
        $dbmigrator->migrate();

        $this->assertTrue(Database::tableExists('employees'));

        $dbmigrator = new DBMigrator(
            'dbmigrator_test',
            \App\Utils\Path::resolve(self::DB_MIGRATOR_DOWN_DIR)
        );
        $dbmigrator->rollback('002.sql');

        $columns = Database::getColumnNames('employees');
        $this->assertCount(5, $columns);
        $this->assertNotContains('email', $columns);
    }

    public function testMigrateThrowsSQLException(): void {
        $dbmigrator = new DBMigrator(
            'dbmigrator_test',
            \App\Utils\Path::resolve(self::DB_MIGRATOR_FAILED_UP_DIR)
        );
        $this->expectException(SqlException::class);
        $dbmigrator->migrate();
    }

    public function testRollbackThrowsSQLException(): void {
        $dbmigrator = new DBMigrator(
            'dbmigrator_test',
            \App\Utils\Path::resolve(self::DB_MIGRATOR_FAILED_UP_DIR)
        );
        $dbmigrator->migrate('001.sql');

        $dbmigrator = new DBMigrator(
            'dbmigrator_test',
            \App\Utils\Path::resolve(self::DB_MIGRATOR_FAILED_DOWN_DIR)
        );

        $this->expectException(SqlException::class);
        $dbmigrator->rollback();
    }

    public function testResetDbTrackAll(): void {
        $this->assertGreaterThanOrEqual(
            31,
            Database::getNumRows(
                Database::selectAll('dbtrack', ['id'])
            )
        );

        $dbmigrator = new DBMigrator(
            'dbmigrator_test',
            \App\Utils\Path::resolve(self::DB_MIGRATOR_FAILED_UP_DIR)
        );

        $dbmigrator->resetDBTrackAll();
        $this->assertEquals(
            0,
            Database::getNumRows(
                Database::selectAll('dbtrack', ['id'])
            )
        );
    }
}
