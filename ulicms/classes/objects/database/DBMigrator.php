<?php

declare(strict_types=1);

use UliCMS\Exceptions\SqlException;

class DBMigrator {

    private $component = null;
    private $folder = null;
    private $strictMode = true;

    public function __construct(string $component, string$folder) {
        $this->component = $component;
        $this->folder = $folder;
        $cfg = new CMSConfig();
        if (isset($cfg->dbmigrator_strict_mode)) {
            $this->strictMode = boolval($cfg->dbmigrator_strict_mode);
        }
    }

    public function enableStrictMode(): void {
        $this->strictMode = true;
    }

    public function disableStrictMode(): void {
        $this->strictMode = false;
    }

    public function migrate(?string $stop = null): void {
        $this->checkVars();
        $files = scandir($this->folder);
        natcasesort($files);
        foreach ($files as $file) {
            $this->executeSqlScript($file);
            if ($file === $stop) {
                return;
            }
        }
    }

    public function executeSqlScript(string $file): void {
        if (endsWith($file, ".sql")) {
            $sql = "SELECT id from {prefix}dbtrack where component = ? and name = ?";
            $args = array(
                $this->component,
                $file
            );
            $result = Database::tableExists("dbtrack") ?
                    Database::pQuery($sql, $args, true) : false;
            if (!$result or Database::getNumRows($result) == 0) {
                $path = $this->folder . "/" . $file;
                $sql = file_get_contents($path);
                $cfg = new CMSConfig();
                $sql = str_ireplace("{prefix}", $cfg->db_prefix, $sql);

                $success = Database::multiQuery($sql, true);
                while (mysqli_more_results(Database::getConnection())) {
                    mysqli_next_result(Database::getConnection());
                }
                if ($success) {
                    $sql = "INSERT INTO {prefix}dbtrack (component, name) values (?,?)";
                    Database::pQuery($sql, $args, true);
                } else if ($this->strictMode) {
                    throw new SqlException("{$this->component} - {$file}: " . Database::getLastError());
                }
            }
        }
    }

    public function rollback(?string $stop = null): void {
        $this->checkVars();
        $files = scandir($this->folder);
        natcasesort($files);
        $files = array_reverse($files);
        foreach ($files as $file) {
            if (endsWith($file, ".sql")) {
                $sql = "SELECT id from {prefix}dbtrack where component = ? and name = ?";
                $args = array(
                    $this->component,
                    $file
                );
                $result = Database::pQuery($sql, $args, true);
                if (Database::getNumRows($result) > 0) {
                    $path = $this->folder . "/" . $file;
                    $sql = file_get_contents($path);
                    $cfg = new CMSConfig();
                    $sql = str_ireplace("{prefix}", $cfg->db_prefix, $sql);
                    $success = Database::multiQuery($sql, true);
                    while (mysqli_more_results(Database::getConnection())) {
                        mysqli_next_result(Database::getConnection());
                    }
                    if ($success or ! $this->strictMode) {
                        $sql = "DELETE FROM {prefix}dbtrack where component = ? and name = ?";
                        Database::pQuery($sql, $args, true);
                    } else if ($this->strictMode) {
                        throw new SqlException("{$this->component} - {$file}: " . Database::getLastError());
                    }
                }
            }
            if ($file === $stop) {
                return;
            }
        }
    }

    public function resetDBTrack(): mysqli_result {
        return Database::pQuery("DELETE FROM {prefix}dbtrack where component = ?", array(
                    $this->component
                        ), true);
    }

    public function resetDBTrackAll(): void {
        Database::truncateTable("dbtrack");
    }

    private function checkVars(): void {
        if (StringHelper::isNullOrEmpty($this->component)) {
            throw new Exception("component is null or empty");
        }
        if (StringHelper::isNullOrEmpty($this->folder)) {
            throw new Exception("folder is null or empty");
        }
        if (!is_dir($this->folder)) {
            throw new Exception("folder not found " . $this->folder);
        }
    }

}
