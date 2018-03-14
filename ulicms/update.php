<?php
define ( "SKIP_TABLE_CHECK", true );
include_once "init.php";
@set_time_limit ( 0 );

Database::query ( "ALTER TABLE `{prefix}users` DROP COLUMN `avatar_file`", true );
Database::query ( "DELETE FROM `{prefix}settings` where name in ('comment_mode', 'facebook_id', 'disqus_id');" );
Settings::delete ( "disable_html_validation" );
Database::query ( "CREATE TABLE `{prefix}dbtrack` ( `id` INT NOT NULL AUTO_INCREMENT , `component` VARCHAR(150) NOT NULL , `name` VARCHAR(150) NOT NULL , `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;", true );
Settings::set ( "db_schema_version", "2018.3" );

$migrator = new DBMigrator ( "core", "lib/updates/up" );
$migrator->migrate ();

Settings::register ( "min_time_to_fill_form", "0" );

Settings::register ( "smtp_encryption", "" );

// PEAR Mail Feature is removed change to default email mode
if (Settings::get ( "email_mode" ) === "pear_mail") {
	Settings::set ( "email_mode", "internal" );
}

// TODO: words_blacklist konvertieren
// || durch \n ersetzen

// Patch Manager zurücksetzen
$pkg = new PackageManager ();
$pkg->truncateInstalledPatches ();

// @unlink ("update.php");
Request::redirect ( "admin/" );
