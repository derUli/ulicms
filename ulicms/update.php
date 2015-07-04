<?php
define ("SKIP_TABLE_CHECK", true);
include_once "init.php";

$pkg = new PackageManager ();
$pkg->truncateInstalledPatches ();
// @unlink ("update.php");
ulicms_redirect ( "admin/" );
