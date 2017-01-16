<?php
define ( "SKIP_TABLE_CHECK", true );
include_once "init.php";
@set_time_limit ( 0 );

Database::query ( "ALTER TABLE `{prefix}users` DROP COLUMN `avatar_file`", true );
Settings::set ( "db_schema_version", "2017.2" );

// Patch Manager zurücksetzen
$pkg = new PackageManager ();
$pkg->truncateInstalledPatches ();

// @unlink ("update.php");
Request::redirect ( "admin/" );
