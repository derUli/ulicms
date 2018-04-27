<?php

class DBMigrator
{

    private $component = null;

    private $folder = null;

    public function __construct($component, $folder)
    {
        $this->component = $component;
        $this->folder = $folder;
    }

    public function migrate($stop = null)
    {
        $this->checkVars();
        $files = scandir($this->folder);
        natcasesort($files);
        foreach ($files as $file) {
            if (endsWith($file, ".sql")) {
                $sql = "SELECT id from {prefix}dbtrack where component = ? and name = ?";
                $args = array(
                    $this->component,
                    $file
                );
                $result = Database::pQuery($sql, $args, true);
                if (Database::getNumRows($result) == 0) {
                    $path = $this->folder . "/" . $file;
                    $sql = file_get_contents($path);
                    $cfg = new CMSConfig();
                    $sql = str_ireplace("{prefix}", $cfg->db_prefix, $sql);
                    Database::query($sql, true);
                    $sql = "INSERT INTO {prefix}dbtrack (component, name) values (?,?)";
                    Database::pQuery($sql, $args, true);
                }
            }
            if ($file === $stop) {
                return;
            }
        }
    }

    public function rollback($stop = null)
    {
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
                    Database::query($sql, true);
                    $sql = "DELETE FROM {prefix}dbtrack where component = ? and name = ?";
                    Database::pQuery($sql, $args, true);
                }
            }
            if ($file === $stop) {
                return;
            }
        }
    }

    public function resetDBTrack()
    {
        return Database::pQuery("DELETE FROM {prefix}dbtrack where component = ?", array(
            $this->component
        ), true);
    }

    public function resetDBTrackAll()
    {
        Database::truncateTable("dbtrack");
    }

    private function checkVars()
    {
        if (StringHelper::isNullOrEmpty($this->component)) {
            throw new Exception("component is null or empty");
        }
        if (StringHelper::isNullOrEmpty($this->folder)) {
            throw new Exception("folder is null or empty");
        }
        if (! is_dir($this->folder)) {
            throw new Exception("folder not found " . $this->folder);
        }
    }
}
