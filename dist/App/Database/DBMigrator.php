<?php

declare(strict_types=1);

namespace App\Database;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Exceptions\SqlException;
use Database;
use Exception;

/**
 * Migrate database
 */
class DBMigrator {
    private string $component;

    private string $folder;

    // component is an identifier for the module which executes the migrations
    // $folder is the path to an up or down folder
    // containing numbered sql scripts from 001.sql to 999.sql
    public function __construct(string $component, string $folder) {
        $this->component = $component;
        $this->folder = $folder;
    }

   /** Run migrations
    *
    * @param string|null $stop last migration to execute, null means execute all
    * @return void
    */
    public function migrate(?string $stop = null): void {
        $this->checkVars();
        $files = scandir($this->folder) ?: [];

        natcasesort($files);
        foreach ($files as $file) {
            $this->executeSqlScript($file);
            if ($file === $stop) {
                return;
            }
        }
    }

    /**
     * Execute a Sql file
     * @param string $file SQL file
     * @throws SqlException
     * @return void
     */
    public function executeSqlScript(string $file): void {
        if (str_ends_with($file, '.sql')) {
            $sql = 'SELECT id from {prefix}dbtrack where component = ? '
                    . 'and name = ?';
            $args = [
                $this->component,
                $file
            ];
            $result = Database::tableExists('dbtrack') ?
                    Database::pQuery($sql, $args, true) : false;
            if (! $result || Database::getNumRows($result) == 0) {
                $path = $this->folder . '/' . $file;
                $sql = (string)file_get_contents($path);
                $sql = str_ireplace('{prefix}', $_ENV['DB_PREFIX'], $sql);

                $success = Database::multiQuery($sql, true);

                while (Database::getConnection() && mysqli_more_results(Database::getConnection())) {
                    mysqli_next_result(Database::getConnection());
                }

                if ($success) {
                    $sql = 'INSERT INTO {prefix}dbtrack (component, name) '
                            . 'values (?,?)';
                    Database::pQuery($sql, $args, true);
                } else {
                    throw new SqlException("{$this->component} - {$file}: " .
                                    Database::getLastError());
                }
            }
        }
    }

    /**
     * Rollback migrations
     * @param string|null $stop Ston on this migration, null means rollback all
     * @throws SqlException
     * @return void
     */
    public function rollback(?string $stop = null): void {
        $this->checkVars();
        $files = scandir($this->folder) ?: [];
        natcasesort($files);
        $files = array_reverse($files);
        foreach ($files as $file) {
            if (str_ends_with($file, '.sql')) {
                $sql = 'SELECT id from {prefix}dbtrack where component = ? '
                        . 'and name = ?';
                $args = [
                    $this->component,
                    $file
                ];
                $result = Database::pQuery($sql, $args, true);
                if (Database::getNumRows($result) > 0) {
                    $path = $this->folder . '/' . $file;
                    $sql = (string)file_get_contents($path);

                    $sql = str_ireplace('{prefix}', $_ENV['DB_PREFIX'], $sql);

                    $success = Database::multiQuery($sql, true);
                    while (Database::getConnection() && mysqli_more_results(Database::getConnection())) {
                        mysqli_next_result(Database::getConnection());
                    }
                    if ($success) {
                        $sql = 'DELETE FROM {prefix}dbtrack '
                                . 'where component = ? and name = ?';
                        Database::pQuery($sql, $args, true);
                    } else {
                        throw new SqlException(
                            "{$this->component} - {$file}: "
                            . Database::getLastError()
                        );
                    }
                }
            }
            if ($file === $stop) {
                return;
            }
        }
    }

    /**
     * Remove migrations of the component from dbtrack
     * @return bool
     */
    public function resetDBTrack(): bool {
        return Database::pQuery('DELETE FROM {prefix}dbtrack '
                        . 'where component = ?', [
                            $this->component
                        ], true);
    }

    /**
     * Truncate dbtrack table
     * @return void
     */
    public function resetDBTrackAll(): void {
        Database::truncateTable('dbtrack');
    }

    public function checkVars(): bool {
        if (empty($this->component)) {
            throw new Exception('component is null or empty');
        }

        if (empty($this->folder)) {
            throw new Exception('folder is null or empty');
        }

        if (! is_dir($this->folder)) {
            throw new Exception('folder not found ' . $this->folder);
        }

        return true;
    }
}
