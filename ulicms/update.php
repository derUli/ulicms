<?php
define ( "SKIP_TABLE_CHECK", true );
include_once "init.php";

@set_time_limit ( 0 );

// Tabelle für Content Type "Liste"
Database::query("CREATE TABLE IF NOT EXISTS `".tbname("lists")."` (
  `content_id` int(11) NOT NULL,
  `language` varchar(50) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `menu` varchar(10) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  UNIQUE KEY `content_id` (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;")

setconfig ( "db_schema_version", "9.8.4" );

// Patch Manager zurücksetzen
$pkg = new PackageManager ();
$pkg->truncateInstalledPatches ();

// @unlink ("update.php");
Request::redirect ( "admin/" );
