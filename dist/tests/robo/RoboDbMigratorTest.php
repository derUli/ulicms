<?php

require_once __DIR__ . '/RoboTestFile.php';
require_once __DIR__ . '/RoboTestBase.php';

class RoboDbMigratorTest extends RoboTestBase {
    protected function tearDown(): void {
        Database::dropTable('employees');
    }

    public function testDbMigratorList(): void {
        $output = $this->runRoboCommand(['dbmigrator:list', 'core']);
        $this->assertStringContainsString('021.sql', $output);
        $this->assertStringContainsString('001.sql', $output);
        $this->assertGreaterThanOrEqual(21, substr_count($output, 'core'));
    }

    public function testMigrate(): void {
        $this->migrateUp();
        $this->migrateDown();
    }

    public function testMigrateFails(): void {
        $dir = \App\Utils\Path::resolve('ULICMS_ROOT/tests/fixtures/failed_migrations');
        $this->migrateUpFails($dir);
        $this->migrateDownFails($dir);
        $this->resetDbTrack($dir);
    }

    public function testDbMigratorReset(): void {
        if (! $this->shouldDropDbOnShutdown()) {
            $this->markTestSkipped();
        }

        $output = $this->runRoboCommand(['dbmigrator:reset']);
        $this->assertStringContainsString('TRUNCATE TABLE', $output);

        $this->assertEquals(
            0,
            Database::getNumRows(
                Database::selectAll('dbtrack')
            )
        );
    }

    protected function migrateUp(): void {
        $dir = \App\Utils\Path::resolve('ULICMS_ROOT/tests/fixtures/migrations');
        $output = $this->runRoboCommand(['dbmigrator:up', 'robo_test', $dir]);

        $this->assertStringContainsString(
            'CREATE TABLE',
            $output
        );

        $this->assertStringContainsString(
            'ALTER TABLE',
            $output
        );
        $this->assertTrue(Database::tableExists('employees'));
    }

    protected function migrateDown(): void {
        $dir = \App\Utils\Path::resolve('ULICMS_ROOT/tests/fixtures/migrations');
        $output = $this->runRoboCommand(['dbmigrator:down', 'robo_test', $dir]);

        $this->assertStringContainsString(
            "set email = 'foo@bar.de'",
            $output
        );
        $this->assertStringContainsString(
            'DROP TABLE',
            $output
        );

        $this->assertFalse(Database::tableExists('employees'));
    }

    protected function migrateUpFails(string $dir): void {
        $output = $this->runRoboCommand(['dbmigrator:up', 'robo_test', $dir]);

        $this->assertStringContainsString(
            'You have an error in your SQL syntax;',
            $output
        );
    }

    protected function migrateDownFails(string $dir): void {
        $output = $this->runRoboCommand(['dbmigrator:down', 'robo_test', $dir]);

        $this->assertStringContainsString(
            'Unknown table',
            $output
        );
    }

    protected function resetDbTrack(string $dir): void {
        $output = $this->runRoboCommand(['dbmigrator:reset', 'robo_test']);
        $this->assertStringContainsString('DELETE FROM', $output);
        $this->assertStringContainsString("where component = 'robo_test'", $output);
    }
}
