<?php
define ( "SKIP_TABLE_CHECK", true );
include_once "init.php";

@set_time_limit ( 0 );

Database::query ( "ALTER TABLE `" . tbname ( "users" ) . "` DROP `icq_id`" );

Settings::set ( "db_schema_version", "100" );

// Patch Manager zurücksetzen
$pkg = new PackageManager ();
$pkg->truncateInstalledPatches ();

// @unlink ("update.php");
Request::redirect ( "admin/" );
