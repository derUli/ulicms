<?php

class Database
{

    private static $connection = null;

    // Connect with database server
    public static function connect($server, $user, $password, $port, $socket = null)
    {
        self::$connection = mysqli_connect($server, $user, $password, "", $port, $socket);
        if (! self::$connection) {
            return false;
        }
        self::query("SET NAMES 'utf8mb4'");
        // sql_mode auf leer setzen, da sich UliCMS nicht im strict_mode betreiben lässt
        self::query("SET SESSION sql_mode = '';");
        
        return self::$connection;
    }

    public static function close()
    {
        mysqli_close(self::$connection);
    }

    // Abstraktion für Ausführen von SQL Strings
    public static function query($sql, $replacePrefix = false)
    {
        $cfg = new CMSConfig();
        if ($replacePrefix) {
            $sql = str_replace("{prefix}", $cfg->db_prefix, $sql);
        }
        $logger = LoggerRegistry::get("sql_log");
        if ($logger) {
            $logger->info($sql);
        }
        return mysqli_query(self::$connection, $sql);
    }

    // execute a sql string with multiple statements
    public static function multiQuery($sql, $replacePrefix = false)
    {
        $cfg = new CMSConfig();
        if ($replacePrefix) {
            $sql = str_replace("{prefix}", $cfg->db_prefix, $sql);
        }
        $logger = LoggerRegistry::get("sql_log");
        if ($logger) {
            $logger->info($sql);
        }
        return mysqli_multi_query(self::$connection, $sql);
    }

    public static function getConnection()
    {
        return self::$connection;
    }

    public static function setConnection($con)
    {
        self::$connection = $con;
    }

    public static function pQuery($sql, $args = array(), $replacePrefix = false)
    {
        $preparedQuery = "";
        $chars = mb_str_split($sql);
        $i = 0;
        foreach ($chars as $char) {
            if ($char != "?") {
                $preparedQuery .= $char;
            } else {
                $value = $args[$i];
                if (is_float($value)) {
                    $value = str_replace(",", ".", floatval($value));
                } else if (is_int($value)) {
                    $value = intval($value);
                } else if (is_bool($value)) {
                    $value = (int) $value;
                } else if (is_null($value)) {
                    $value = "NULL";
                } else {
                    $value = "'" . self::escapeValue($value) . "'";
                }
                $preparedQuery .= $value;
                $i ++;
            }
        }
        return Database::query($preparedQuery, $replacePrefix);
    }

    public static function getServerVersion()
    {
        return mysqli_get_server_info(self::$connection);
    }

    public static function getClientInfo()
    {
        return mysqli_get_client_info(self::$connection);
    }

    public static function getClientVersion()
    {
        return mysqli_get_client_version(self::$connection);
    }

    public static function dropTable($table, $prefix = true)
    {
        if ($prefix) {
            $table = tbname($table);
        }
        
        $table = self::escapeName($table);
        return self::query("DROP TABLE $table");
    }

    public static function selectAVG($table, $column, $where = "", $prefix = true)
    {
        if ($prefix) {
            $table = tbname($table);
        }
        
        $table = self::escapeName($table);
        $column = self::escapeName($column);
        $sql = "select avg($column) from $table";
        if (StringHelper::isNotNullOrEmpty($where)) {
            $sql .= " where $where";
        }
        $result = Database::query($sql);
        return $result;
    }

    public static function selectMin($table, $column, $where = "", $prefix = true)
    {
        if ($prefix) {
            $table = tbname($table);
        }
        
        $table = self::escapeName($table);
        $column = self::escapeName($column);
        $sql = "select min($column) from $table";
        if (StringHelper::isNotNullOrEmpty($where)) {
            $sql .= " where $where";
        }
        $result = Database::query($sql);
        return $result;
    }

    public static function deleteFrom($table, $where = "", $prefix = true)
    {
        if ($prefix) {
            $table = tbname($table);
        }
        $table = self::escapeName($table);
        
        $sql = "DELETE FROM $table";
        
        if (StringHelper::isNotNullOrEmpty($where)) {
            $sql .= " where $where";
        }
        $result = Database::query($sql);
        return $result;
    }

    public static function selectMax($table, $column, $where = "", $prefix = true)
    {
        if ($prefix) {
            $table = tbname($table);
        }
        
        $table = self::escapeName($table);
        $column = self::escapeName($column);
        $sql = "select min($column) from $table";
        if (StringHelper::isNotNullOrEmpty($where)) {
            $sql .= " where $where";
        }
        $result = Database::query($sql);
        return $result;
    }

    public static function truncateTable($table, $prefix = true)
    {
        if ($prefix) {
            $table = tbname($table);
        }
        
        $table = self::escapeName($table);
        return self::query("TRUNCATE TABLE $table");
    }

    public static function dropColumn($table, $column, $prefix = true)
    {
        if ($prefix) {
            $table = tbname($table);
        }
        
        $column = self::escapeName($column);
        $table = self::escapeName($table);
        return self::query("ALTER TABLE $table DROP COLUMN $column");
    }

    public static function selectAll($table, $columns = array(), $where = "", $args = array(), $prefix = true, $order = "")
    {
        if ($prefix) {
            $table = tbname($table);
        }
        $table = self::escapeName($table);
        if (count($columns) == 0) {
            $columns[] = "*";
        }
        
        $columns_sql = implode(", ", $columns);
        
        $sql = "select $columns_sql from $table";
        if (StringHelper::isNotNullOrEmpty($where)) {
            $sql .= " where $where ";
        }
        if (! empty($order)) {
            $sql .= " order by {$order}";
        }
        return self::pQuery($sql, $args);
    }

    public static function escapeName($name)
    {
        $name = str_replace("'", "", $name);
        $name = str_replace("\"", "", $name);
        $name = "`" . db_escape($name) . "`";
        return $name;
    }

    public static function getLastInsertID()
    {
        return mysqli_insert_id(self::$connection);
    }

    public static function getInsertID()
    {
        return self::getLastInsertID();
    }

    // Fetch Row in diversen Datentypen
    public static function fetchArray($result)
    {
        return mysqli_fetch_array($result);
    }

    public static function fetchField($result)
    {
        return mysqli_fetch_field($result);
    }

    public static function fetchAssoc($result)
    {
        return mysqli_fetch_assoc($result);
    }

    public static function fetchAll($result, $resulttype = MYSQLI_NUM)
    {
        if (function_exists("mysqli_fetch_all")) {
            return mysqli_fetch_all($result, $resulttype);
        }
        
        // @FIXME : $resulttype in alternativer Implementation von fetch_all behandeln
        $retval = array();
        while ($row = self::fetchAssoc($result)) {
            $retval[] = $row;
        }
        
        return $retval;
    }

    // Datenbank auswählen
    public static function select($schema)
    {
        return mysqli_select_db(self::$connection, $schema);
    }

    public static function getNumFieldCount($result)
    {
        return mysqli_field_count(self::$connection);
    }

    public static function getAffectedRows()
    {
        return mysqli_affected_rows(self::$connection);
    }

    public static function fetchObject($result)
    {
        return mysqli_fetch_object($result);
    }

    public static function fetchRow($result)
    {
        return mysqli_fetch_row($result);
    }

    public static function getNumRows($result)
    {
        return mysqli_num_rows($result);
    }

    public static function getLastError()
    {
        return mysqli_error(self::$connection);
    }

    public static function error()
    {
        return self::getLastError();
    }

    public static function getError()
    {
        return self::getLastError();
    }

    public static function getAllTables()
    {
        $tableList = array();
        $res = mysqli_query(self::$connection, "SHOW TABLES");
        while ($cRow = mysqli_fetch_array($res)) {
            $tableList[] = $cRow[0];
        }
        
        sort($tableList);
        return $tableList;
    }

    // Abstraktion für Escapen von Werten
    public static function escapeValue($value, $type = null)
    {
        if (is_null($value)) {
            return "NULL";
        }
        if (is_null($type)) {
            if (is_float($value)) {
                return floatval($value);
            } else if (is_int($value)) {
                return intval($value);
            } else if (is_bool($value)) {
                return (int) $value;
            } else {
                return mysqli_real_escape_string(self::$connection, $value);
            }
        } else {
            if ($type === DB_TYPE_INT) {
                return intval($value);
            } else if ($type === DB_TYPE_FLOAT) {
                return floatval($value);
            } else if ($type === DB_TYPE_STRING) {
                return mysqli_real_escape_string(self::$connection, $value);
            } else if ($type === DB_TYPE_BOOL) {
                return intval($value);
            } else {
                return $value;
            }
        }
    }

    public static function getColumnNames($table, $prefix = true)
    {
        $retval = array();
        if ($prefix) {
            $table = tbname($table);
        }
        $query = Database::query("SELECT * FROM $table limit 1");
        $fields_num = self::getNumFieldCount($query);
        if ($fields_num > 0) {
            for ($i = 0; $i < $fields_num; $i ++) {
                $field = db_fetch_field($query);
                $retval[] = $field->name;
            }
            sort($retval);
        }
        return $retval;
    }

    public static function fetchSingle($result)
    {
        if (self::getNumRows($result) > 1) {
            throw new RangeException("Result contains more than one element.");
        }
        if (Database::getNumRows($result) == 1) {
            return self::fetchObject($result);
        }
        return null;
    }

    public static function fetchSingleOrDefault($result, $default = null)
    {
        if (self::getNumRows($result) > 1) {
            throw new RangeException("Result contains more than one element.");
        }
        if (Database::getNumRows($result) == 1) {
            return self::fetchObject($result);
        }
        return $default;
    }

    public static function fetchFirst($result)
    {
        if (Database::getNumRows($result) > 0) {
            return self::fetchObject($result);
        }
        return null;
    }

    public static function fetchFirstOrDefault($result, $default = null)
    {
        if (Database::getNumRows($result) > 0) {
            return self::fetchObject($result);
        }
        return $default;
    }

    public static function any($result)
    {
        return (Database::getNumRows($result) > 0);
    }

    public static function hasMoreResults()
    {
        return mysqli_more_results(self::$connection);
    }

    public static function loadNextResult()
    {
        return mysqli_next_result(self::$connection);
    }

    public static function storeResult()
    {
        return mysqli_store_result(self::$connection);
    }
}

// Alias für Database
class DB extends Database
{
}