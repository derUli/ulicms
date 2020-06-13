<?php

declare(strict_types=1);

use UliCMS\Exceptions\SqlException;

// this class provides an abstraction for database access
// and many helpful utility methods to do database stuff
class Database {

    private static $connection = null;
    private static $echoQueries = false;
    private static $schema_selected = false;

    // this is used to show sql queries while running the unit tests
    public static function setEchoQueries($echoQueries = true): void {
        self::$echoQueries = $echoQueries;
    }

    // force MySQL into strict mode
    public static function getSqlStrictModeFlags(): array {
        return [
            "ONLY_FULL_GROUP_BY",
            "STRICT_TRANS_TABLES",
            "NO_ZERO_IN_DATE",
            "NO_ZERO_DATE",
            "ERROR_FOR_DIVISION_BY_ZERO",
            "NO_AUTO_CREATE_USER",
            "NO_ENGINE_SUBSTITUTION"
        ];
    }

    // Connect with database server
    public static function connect(
            string $server,
            string $user,
            string $password,
            int $port,
            ?string $socket = null,
            bool $db_strict_mode = false
    ): ?mysqli {
        $connected = mysqli_connect($server, $user, $password, "", $port, $socket);
        self::$connection = $connected ? $connected : null;
        if (!self::$connection) {
            return null;
        }

        self::query("SET NAMES 'utf8mb4'");
        // sql_mode auf leer setzen, da sich UliCMS nicht im
        // strict_mode betreiben lässt
        if ($db_strict_mode) {
            self::pQuery("SET SESSION sql_mode = ?", [
                implode(",", self::getSqlStrictModeFlags())
            ]);
        } else {
            self::query("SET SESSION sql_mode = ''");
        }

        return self::$connection;
    }

    // Close the databse connection
    public static function close(): void {
        mysqli_close(self::$connection);
        self::$connection = null;
    }

    // Create a database schema
    public static function createSchema($name): bool {
        return Database::query("CREATE DATABASE {$name}");
    }

    // TODO: Do logging when auto initialize the database
    public static function setupSchemaAndSelect(
            string $schemaName,
            array $otherScripts = []
    ): bool {
        $selected = self::select($schemaName);
        if (!$selected) {
            $success = self::createSchema($schemaName);
            if ($success) {
                $selected = self::select($schemaName);
            }
        }

        if ($selected) {
            $migrator = new DBMigrator(
                    "core",
                    Path::resolve("ULICMS_ROOT/lib/migrations/up")
            );
            $migrator->migrate();
            foreach ($otherScripts as $script) {
                $fullPath = Path::resolve($script);
                $migrator = new DBMigrator("core/initial", dirname($fullPath));

                $migrator->executeSqlScript(Path::resolve(basename($fullPath)));
            }
        }

        return $selected;
    }

    // Abstraktion für Ausführen von SQL Strings
    public static function query(string $sql, bool $replacePrefix = false) {
        $cfg = new CMSConfig();
        if ($replacePrefix) {
            $sql = str_replace("{prefix}", $cfg->db_prefix, $sql);
        }
        $logger = LoggerRegistry::get("sql_log");
        if ($logger) {
            $logger->info($sql);
        }
        if (self::$echoQueries) {
            echo $sql . "\n";
        }
        $result = mysqli_query(self::$connection, $sql);
        if (!$result) {
            throw new SqlException(self::getError());
        }
        return $result;
    }

    // execute a sql string with multiple statements
    public static function multiQuery(
            string $sql,
            bool $replacePrefix = false
    ) {
        $cfg = new CMSConfig();
        if ($replacePrefix) {
            $sql = str_replace("{prefix}", $cfg->db_prefix, $sql);
        }
        $logger = LoggerRegistry::get("sql_log");
        if ($logger) {
            $logger->info($sql);
        }
        if (self::$echoQueries) {
            echo $sql . "\n";
        }
        return mysqli_multi_query(self::$connection, $sql);
    }

    // get the current database connection
    public static function getConnection(): ?mysqli {
        return self::$connection;
    }

    // returns true if UliCMS is connected to database
    public static function isConnected(): bool {
        return !is_null(self::$connection);
    }

    public static function setConnection(?mysqli $con): void {
        self::$connection = $con;
    }

    // prepared statements
    // $sql must be a sql string containg question marks
    // $args must be an array of values
    // please ensure, that all values in $args have the type they
    // are intended to have. e.g numbers must be int or float
    // the method replaces the question marks with the items of the $args array
    // $args are automatically escaped to prevent sql injections
    public static function pQuery(
            string $sql,
            array $args = [],
            bool $replacePrefix = false
    ) {
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
                $i++;
            }
        }
        return Database::query($preparedQuery, $replacePrefix);
    }

    public static function getServerVersion(): ?string {
        return mysqli_get_server_info(self::$connection);
    }

    public static function getClientInfo(): string {
        return mysqli_get_client_info(self::$connection);
    }

    public static function getClientVersion(): ?int {
        return mysqli_get_client_version(self::$connection);
    }

    public static function dropTable(
            string $table,
            bool $prefix = true
    ): bool {
        if ($prefix) {
            $table = tbname($table);
        }

        if (!faster_in_array($table, self::getAllTables())) {
            return true;
        }

        $table = self::escapeName($table);
        return self::query("DROP TABLE IF EXISTS $table");
    }

    public static function dropSchema(string $schema): bool {
        $schema = self::escapeName($schema);
        return self::query("DROP SCHEMA IF EXISTS $schema ");
    }

    public static function selectMin(
            string $table,
            string $column,
            string $where = "",
            bool $prefix = true
    ) {
        if ($prefix) {
            $table = tbname($table);
        }

        $table = self::escapeName($table);
        $column = self::escapeName($column);
        $sql = "select min($column) as val from $table";
        if (StringHelper::isNotNullOrEmpty($where)) {
            $sql .= " where $where";
        }

        $result = Database::query($sql);

        $row = Database::fetchObject($result);
        $val = $row->val;
        return is_decimal($val) ? floatval(val) : intval($val);
    }

    public static function selectMax(
            string $table,
            string $column,
            string $where = "",
            bool $prefix = true
    ) {
        if ($prefix) {
            $table = tbname($table);
        }

        $table = self::escapeName($table);
        $column = self::escapeName($column);
        $sql = "select max($column) as val from $table";
        if (StringHelper::isNotNullOrEmpty($where)) {
            $sql .= " where $where";
        }

        $result = Database::query($sql);

        $row = Database::fetchObject($result);
        $val = $row->val;
        return is_decimal($val) ? floatval(val) : intval($val);
    }

    public static function selectAvg(
            string $table,
            string $column,
            string $where = "",
            bool $prefix = true
    ) {
        if ($prefix) {
            $table = tbname($table);
        }

        $table = self::escapeName($table);
        $column = self::escapeName($column);
        $sql = "select avg($column) as val from $table";
        if (StringHelper::isNotNullOrEmpty($where)) {
            $sql .= " where $where";
        }

        $result = Database::query($sql);

        $row = Database::fetchObject($result);
        $val = $row->val;
        return is_decimal($val) ? floatval($val) : intval($val);
    }

    public static function deleteFrom(
            string $table,
            string $where = "",
            bool $prefix = true
    ): bool {
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

    public static function truncateTable(
            string $table,
            bool $prefix = true
    ): bool {
        if ($prefix) {
            $table = tbname($table);
        }

        $table = self::escapeName($table);
        return self::query("TRUNCATE TABLE $table");
    }

    public static function dropColumn(
            string $table,
            string $column,
            bool $prefix = true
    ): bool {
        if ($prefix) {
            $table = tbname($table);
        }

        $column = self::escapeName($column);
        $table = self::escapeName($table);
        return self::query("ALTER TABLE $table DROP COLUMN $column");
    }

    public static function selectAll(
            string $table,
            array $columns = [],
            string $where = "",
            array $args = [],
            bool $replacePrefix = true,
            string $order = ""
    ): ?mysqli_result {

        if ($replacePrefix) {
            $table = tbname($table);
        }
        $table = self::escapeName($table);

        if (count($columns) == 0) {
            $columns[] = "*";
        }

        $columns_sql = implode(", ", $columns);

        $sql = "select $columns_sql from $table";

        if (StringHelper::isNotNullOrWhitespace($where)) {
            $sql .= " where $where ";
        }

        if (StringHelper::isNotNullOrWhitespace($order)) {
            $sql .= " order by $order";
        }
        return self::pQuery($sql, $args, $replacePrefix);
    }

    public static function escapeName(string $name): string {
        $name = str_replace("'", "", $name);
        $name = str_replace("\"", "", $name);
        $name = "`" . db_escape($name) . "`";
        return $name;
    }

    public static function getLastInsertID(): ?int {
        return mysqli_insert_id(self::$connection);
    }

    public static function getInsertID(): ?int {
        return self::getLastInsertID();
    }

// Fetch Row in diversen Datentypen
    public static function fetchArray(?mysqli_result $result) {
        return mysqli_fetch_array($result);
    }

    public static function fetchField(?mysqli_result $result) {
        return mysqli_fetch_field($result);
    }

    public static function fetchAssoc(?mysqli_result $result) {
        return mysqli_fetch_assoc($result);
    }

    public static function fetchAll(?mysqli_result $result): array {
        $datasets = [];

        while (!is_null($result) and $row = self::fetchObject($result)) {
            $datasets[] = $row;
        }

        return $datasets;
    }

    // Datenbank auswählen
    public static function select(string $schema): bool {
        $selected = mysqli_select_db(self::$connection, $schema);
        self::$schema_selected = $selected;
        return $selected;
    }

    public static function isSchemaSelected(): bool {
        return self::$schema_selected ;
    }
    public static function getNumFieldCount(): ?int {
        return mysqli_field_count(self::$connection);
    }

    public static function getAffectedRows(): ?int {
        return mysqli_affected_rows(self::$connection);
    }

    public static function fetchObject(mysqli_result $result): ?object {
        return mysqli_fetch_object($result);
    }

    public static function fetchRow(mysqli_result $result) {
        return mysqli_fetch_row($result);
    }

    public static function getNumRows(mysqli_result $result): ?int {
        return mysqli_num_rows($result);
    }

    // returns the last mysqli error
    public static function getLastError(): ?string {
        return mysqli_error(self::$connection);
    }

    // returns the last mysqli error
    public static function error(): ?string {
        return self::getLastError();
    }

    // returns the last mysqli error
    public static function getError(): ?string {
        return self::getLastError();
    }

    // returns a list of all tables in the database
    public static function getAllTables(): array {
        $tableList = [];
        $res = mysqli_query(self::$connection, "SHOW TABLES");
        while ($cRow = mysqli_fetch_array($res)) {
            $tableList[] = $cRow[0];
        }

        sort($tableList);
        return $tableList;
    }

    // returns true if a database table exists
    public static function tableExists(string $table,
            bool $prefix = true): bool {
        $tableName = $prefix ? tbname($table) : $table;
        return faster_in_array($tableName, self::getAllTables());
    }

    // escape values to prevent sql injections
    // don't manually call this, if you use
    // pQuery() to make queries
    public static function escapeValue($value, ?int $type = null) {
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
                return mysqli_real_escape_string(
                        self::$connection,
                        strval($value)
                );
            } else if ($type === DB_TYPE_BOOL) {
                return intval($value);
            } else {
                return $value;
            }
        }
    }

    // returns a list of all tables of a table
    public static function getColumnNames(
            string $table,
            bool $prefix = true
    ): array {
        $retval = [];
        if ($prefix) {
            $table = tbname($table);
        }
        $result = Database::query("SELECT * FROM $table limit 1");
        $fields_num = self::getNumFieldCount();
        if ($fields_num > 0) {
            for ($i = 0; $i < $fields_num; $i++) {
                $field = db_fetch_field($result);
                $retval[] = $field->name;
            }
            sort($retval);
        }
        return $retval;
    }

    // if the result contains one result returns it
    // if the result contains no result returns null
    // if the result returns more than one result throws an exception
    public static function fetchSingle(?mysqli_result $result): ?object {
        if (self::getNumRows($result) > 1) {
            throw new RangeException("Result contains more than one element.");
        }
        if (Database::getNumRows($result) == 1) {
            return self::fetchObject($result);
        }
        return null;
    }

    // if the result contains one result returns it
    // if the result contains no result returns a default object
    // if the result returns more than one result throws an exception
    public static function fetchSingleOrDefault(
            ?mysqli_result $result,
            ?object $default = null
    ): ?object {
        if (self::getNumRows($result) > 1) {
            throw new RangeException("Result contains more than one element.");
        }
        if (Database::getNumRows($result) == 1) {
            return self::fetchObject($result);
        }
        return $default;
    }

    // fetches and returns the first dataset of a mysqli_result
    // as object
    public static function fetchFirst(mysqli_result $result): ?object {
        if (Database::getNumRows($result) > 0) {
            return self::fetchObject($result);
        }
        return null;
    }

    // fetches and returns the first dataset of a mysqli_result
    // as object
    // if there are no results, return a default object
    public static function fetchFirstOrDefault(
            mysqli_result$result,
            ?object $default = null
    ): ?object {
        if (Database::getNumRows($result) > 0) {
            return self::fetchObject($result);
        }
        return $default;
    }

    // returns true if the database result contains at least one row
    public static function any(mysqli_result $result): bool {
        return (Database::getNumRows($result) > 0);
    }

    // used for multi queries
    public static function hasMoreResults(): bool {
        return mysqli_more_results(self::$connection);
    }

    // used for multi queries
    public static function loadNextResult(): bool {
        return mysqli_next_result(self::$connection);
    }

    // used for multi queries
    public static function storeResult(): mysqli_result {
        return mysqli_store_result(self::$connection);
    }

}

// for backwards compatiblity
class_alias(Database::class, "DB");
