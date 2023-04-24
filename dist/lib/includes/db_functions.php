<?php

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

define('DB_TYPE_INT', 1);
define('DB_TYPE_FLOAT', 2);
define('DB_TYPE_STRING', 3);
define('DB_TYPE_BOOL', 4);

// database api functions
// all functions in this file are deprecated you should
// use the Database class instead.
function db_query(string $query) {
    return Database::query($query);
}

function db_fetch_object(?mysqli_result $result) {
    return Database::fetchObject($result);
}

/**
 * Prepend the table prefix to a database table name
 *
 * @param string $name
 *
 * @return string
 */
function tbname(string $name): string {
    return $_ENV['DB_PREFIX'] . $name;
}

/**
 * Escape strings for sql queries
 *
 * @param mixed $value
 * @param ?string $type
 *
 * @return string
 */
function db_escape($value, ?string $type = null): string {
    return Database::escapeValue($value, $type);
}
