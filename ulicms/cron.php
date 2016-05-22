<?php
require_once "init.php";
fcflush ();

if (Settings::get ( "delete_ips_after_48_hours" )) {
	db_query ( "Update " . tbname ( "log" ) . " SET ip = NULL WHERE DATEDIFF(NOW(), zeit) >= 2" );
}

$empty_trash_days = Settings::get ( "empty_trash_days" );

if ($empty_trash_days === false) {
	$empty_trash_days = 30;
}

// Papierkorb für Seiten Cronjob
$empty_trash_timestamp = $empty_trash_days * (60 * 60 * 24);
db_query ( "DELETE FROM " . tbname ( "content" ) . " WHERE " . time () . " -  `deleted_at` > $empty_trash_timestamp" ) or die ( db_error () );

// Alle Revisionen von bereits gelöschten Seiten entfernen
db_query ( "DELETE FROM " . tbname ( "history" ) . " WHERE content_id NOT IN (
            SELECT id from " . tbname ( "content" ) . ");" );

// Cronjobs der Module
add_hook ( "cron" );

$version = new ulicms_version ();

$developmentVersion = "";

if ($version->getDevelopmentVersion ()) {
	$developmentVersion = " Vorabversion";
}

if (! is_file ( "init.php" ) and ! is_dir ( "lib" )) {
	exit ();
}

exit ();