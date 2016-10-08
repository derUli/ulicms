<?php
define ( "SKIP_TABLE_CHECK", true );

include_once "init.php";
@set_time_limit ( 0 );

// Database Changes of 9.8.6
Database::query ( "ALTER TABLE `" . tbname ( "users" ) . "` DROP COLUMN `icq_id`" );
Database::query ( "ALTER TABLE `" . tbname ( "settings" ) . "` ADD UNIQUE (`name`)" );
Database::query ( "ALTER TABLE `" . tbname ( "content" ) . " ` ADD COLUMN `only_admins_can_edit` tinyint(1) NOT NULL DEFAULT '0'" );

Settings::set ( "db_schema_version", "9.8.6" );

// Patch Manager zurücksetzen
$pkg = new PackageManager ();
$pkg->truncateInstalledPatches ();

// @unlink ("update.php");
Request::redirect ( "admin/" );
