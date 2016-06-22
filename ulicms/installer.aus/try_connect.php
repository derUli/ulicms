<?php
if ($_SERVER ["REQUEST_METHOD"] != "POST") {
	die ( "Only Post Requests Allowed" );
}
session_start ();
setcookie ( session_name (), session_id () );
error_reporting ( E_ALL ^ E_NOTICE );

if (! isset ( $_SESSION ["language"] )) {
	$_SESSION ["language"] = "de";
}

if (isset ( $_GET ["language"] )) {
	$_SESSION ["language"] = basename ( $_GET ["language"] );
}

$file = "lang/" . $_SESSION ["language"] . ".php";

if (! file_exists ( $file )) {
	$file = "lang/de.php";
}

include_once $file;

@$connection = mysqli_connect ( $_POST ["servername"], $_POST ["loginname"], $_POST ["passwort"] );
if ($connection == false) {
	die ( TRANSLATION_DB_CONNECTION_FAILED );
}

// Check if database is present else try to create it.
$query = mysqli_query ( $connection, "SHOW DATABASES" );
$databases = array ();
while ( $row = mysqli_fetch_array ( $query ) ) {
	$databases [] = $row [0];
}

if (! in_array ( $_POST ["datenbank"], $databases )) {
	// Try to create database if it not exists
	mysqli_query ( $connection, "CREATE DATABASE " . mysqli_real_escape_string ( $connection, $_POST ["datenbank"] ) );
}

@$select = mysqli_select_db ( $connection, $_POST ["datenbank"] );

if ($select == false) {
	die ( TRANSLATION_CANT_OPEN_SCHEMA );
}