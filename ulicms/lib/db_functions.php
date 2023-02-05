<?php

define("DB_TYPE_INT", 1);
define("DB_TYPE_FLOAT", 2);
define("DB_TYPE_STRING", 3);
define("DB_TYPE_BOOL", 4);

// database api functions
// all functions in this file are deprecated you should
// use the Database class instead.
function db_query(string $query) {
    return Database::query($query);
}

function db_get_client_info(): string {
    return Database::getClientInfo();
}

function db_name_escape(string $name): string {
    return Database::escapeName($name);
}

function db_last_insert_id(): ?int {
    return Database::getLastInsertID();
}

function db_insert_id(): ?int {
    return Database::getLastInsertID();
}

// Fetch Row in diversen Datentypen
function db_fetch_array(?mysqli_result $result) {
    return Database::fetchArray($result);
}

function db_fetch_field(?mysqli_result $result) {
    return Database::fetchField($result);
}

function db_fetch_assoc(?mysqli_result $result) {
    return Database::fetchAssoc($result);
}

function db_fetch_all(?mysqli_result $result) {
    return Database::fetchAll($result);
}

function db_close(): void {
    Database::close();
}

// Connect with database server
function db_connect(
        string $server,
        string $user,
        string $password,
        int $port = 3306,
        ?string $socket = null,
        bool $db_strict_mode = false
): ?object {
    return Database::connect($server, $user, $password, $port, $socket, $db_strict_mode);
}

// Datenbank auswählen
function db_select(string $schema): bool {
    return Database::select($schema);
}

function db_num_fields(): ?int {
    return Database::getNumFieldCount();
}

function db_affected_rows(): ?int {
    return Database::getAffectedRows();
}

function schema_select(string $schema): bool {
    return Database::select($schema);
}

function db_select_db(string $schema): bool {
    return Database::select($schema);
}

function db_fetch_object(?mysqli_result $result) {
    return Database::fetchObject($result);
}

function db_fetch_row(?mysqli_result $result) {
    return Database::fetchRow($result);
}

function db_num_rows(mysqli_result $result): ?int {
    return Database::getNumRows($result);
}

function db_last_error(): ?string {
    return Database::getLastError();
}

function db_error(): ?string {
    return db_last_error();
}

function db_get_tables(): array {
    return Database::getAllTables();
}

function db_real_escape_string($value): string {
    return Database::escapeValue($value, DB_TYPE_STRING);
}

// prefixes the name of a database table with the table prefix from configuration
//
function tbname(string $name): string {
    $config = new CMSConfig();
    return $config->db_prefix . $name;
}

// Abstraktion für Escapen von Werten
function db_escape($value, ?string $type = null): string {
    return Database::escapeValue($value, $type);
}
