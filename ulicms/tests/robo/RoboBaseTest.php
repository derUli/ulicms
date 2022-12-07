<?php

declare(strict_types=1);

require_once "RoboFile.php";
require_once __DIR__ . "/RoboTestFile.php";

abstract class RoboBaseTest extends \PHPUnit\Framework\TestCase {

    protected function runRoboCommand(array $command): string {
        ob_start();
        passthru(
                "vendor" .
                DIRECTORY_SEPARATOR .
                "bin" .
                DIRECTORY_SEPARATOR .
                "robo " .
                $command[0]
        );
        return trim(ob_get_clean());
    }

    protected function shouldDropDbOnShutdown(): bool {
        $cfg = new CMSConfig();
        return isset($cfg->dbmigrator_drop_database_on_shutdown) &&
                $cfg->dbmigrator_drop_database_on_shutdown;
    }

    protected function resetDb() {
        $cfg = new CMSConfig();
        $additionalSql = is_array($cfg->dbmigrator_initial_sql_files) ?
                $cfg->dbmigrator_initial_sql_files : [];

        Database::dropSchema($cfg->db_database);
        Database::setupSchemaAndSelect(
                $cfg->db_database,
                $additionalSql
        );
    }

}
