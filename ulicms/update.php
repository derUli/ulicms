<?php
define ( "SKIP_TABLE_CHECK", true );
include_once "init.php";

@set_time_limit ( 0 );

// Patch Manager zurücksetzen
$pkg = new PackageManager ();
$pkg->truncateInstalledPatches ();

// @unlink ("update.php");

ulicms_redirect ( "admin/" );
