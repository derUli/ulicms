<?php

defined('ULICMS_ROOT') or exit('no direct script access allowed');

define("DB_TYPE_INT", 1);
define("DB_TYPE_FLOAT", 2);
define("DB_TYPE_STRING", 3);
define("DB_TYPE_BOOL", 4);

// database api functions
// all functions in this file are deprecated you should
// use the Database class instead.
function db_query(string $query)
{
    return Database::query($query);
}

// Fetch Row in diversen Datentypen
function db_fetch_array(?mysqli_result $result)
{
    return Database::fetchArray($result);
}

function db_fetch_field(?mysqli_result $result)
{
    return Database::fetchField($result);
}

function db_fetch_assoc(?mysqli_result $result)
{
    return Database::fetchAssoc($result);
}

function db_num_fields(): ?int
{
    return Database::getNumFieldCount();
}

function db_fetch_object(?mysqli_result $result)
{
    return Database::fetchObject($result);
}

function db_fetch_row(?mysqli_result $result)
{
    return Database::fetchRow($result);
}

function db_num_rows(mysqli_result $result): ?int
{
    return Database::getNumRows($result);
}

/**
 * prepend the table prefix to a database table name
 * @param string $name
 * @return string
 */
function tbname(string $name): string
{
    $config = new CMSConfig();
    return $config->db_prefix . $name;
}

// Abstraktion für Escapen von Werten
function db_escape($value, ?string $type = null): string
{
    return Database::escapeValue($value, $type);
}
