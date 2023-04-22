<?php

declare(strict_types=1);

require_once 'RoboFile.php';
require_once __DIR__ . '/RoboTestFile.php';

use App\Helpers\StringHelper;

class RoboTestBase extends \PHPUnit\Framework\TestCase
{
    protected function runRoboCommand(array $command): string
    {
        $runner = new Robo\Runner(RoboTestFile::class);
        array_unshift($command, Path::resolve('ULICMS_ROOT/vendor/bin/robo'));
        ob_start();
        $runner->execute($command);
        return trim(ob_get_clean());
    }

    protected function shouldDropDbOnShutdown(): bool
    {
        return isset($_ENV['DBMIGRATOR_DROP_DATABASE_ON_SHUTDOWN']) && $_ENV['DBMIGRATOR_DROP_DATABASE_ON_SHUTDOWN'];
    }

    protected function resetDb()
    {
        $additionalSql = isset($_ENV['DBMIGRATOR_INITIAL_SQL_FILES']) ? StringHelper::splitAndTrim($_ENV['DBMIGRATOR_INITIAL_SQL_FILES']) : [];
        $additionalSql = array_map('trim', $additionalSql);

        Database::dropSchema($_ENV['DB_DATABASE']);
        Database::setupSchemaAndSelect(
            $_ENV['DB_DATABASE'],
            $additionalSql
        );
    }
}
