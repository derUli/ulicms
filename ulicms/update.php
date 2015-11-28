<?php
define ( "SKIP_TABLE_CHECK", true );
include_once "init.php";

@set_time_limit ( 0 );

setconfig ( "db_schema_version", "9.8.1" );

// Patch Manager zurücksetzen
$pkg = new PackageManager ();
$pkg->truncateInstalledPatches ();

// @unlink ("update.php");

ulicms_redirect ( "admin/" );
