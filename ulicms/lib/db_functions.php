<?php
define("DB_TYPE_INT", 1);
define("DB_TYPE_FLOAT", 2);
define("DB_TYPE_STRING", 3);
define("DB_TYPE_BOOL", 4);

// database api functions
// all functions in this file are deprecated you should use the Database class instead.
function db_query($query)
{
    return Database::query($query);
}

function db_get_server_info()
{
    return Database::getServerVersion();
}

function db_get_client_info()
{
    return Database::getClientInfo();
}

function db_name_escape($name)
{
    return Database::escapeName($name);
}

function db_last_insert_id()
{
    return Database::getLastInsertID();
}

function db_insert_id()
{
    return Database::getLastInsertID();
}

// Fetch Row in diversen Datentypen
function db_fetch_array($result)
{
    return Database::fetchArray($result);
}

function db_fetch_field($result)
{
    return Database::fetchField($result);
}

function db_fetch_assoc($result)
{
    return Database::fetchAssoc($result);
}

function db_fetch_all($result, $resulttype = MYSQLI_NUM)
{
    return Database::fetchAll($result, $resulttype);
}

function db_close()
{
    Database::close();
}

// Connect with database server
function db_connect($server, $user, $password, $port = 3306)
{
    return Database::connect($server, $user, $password, $port);
}

// Datenbank auswählen
function db_select($schema)
{
    return Database::select($schema);
}

function db_num_fields($result)
{
    return Database::getNumFieldCount($result);
}

function db_affected_rows()
{
    return Database::getAffectedRows();
}

function schema_select($schema)
{
    return Database::select($schema);
}

function db_select_db($schema)
{
    return Database::select($schema);
}

function db_fetch_object($result)
{
    return Database::fetchObject($result);
}

function db_fetch_row($result)
{
    return Database::fetchRow($result);
}

function db_num_rows($result)
{
    return Database::getNumRows($result);
}

function db_last_error()
{
    return Database::getLastError();
}

function db_error()
{
    return db_last_error();
}

function db_get_tables()
{
    $tableList = array();
    $res = mysqli_query(Database::getConnection(), "SHOW TABLES");
    while ($cRow = mysqli_fetch_array($res)) {
        $tableList[] = $cRow[0];
    }
    
    sort($tableList);
    return $tableList;
}

function db_real_escape_string($value)
{
    return Database::escapeValue($value, DB_TYPE_STRING);
}

// Tabellenname zusammensetzen
function tbname($name)
{
    $config = new CMSConfig();
    return $config->db_prefix . $name;
}

// Abstraktion für Escapen von Werten
function db_escape($value, $type = null)
{
    return Database::escapeValue($value, $type);
}