<?php
include_once "init.php";
define("SKIP_TABLE_CHECK", true);

// Move folders to content Folder
rename(ULICMS_ROOT."/modules", ULICMS_ROOT."/content/modules");
rename(ULICMS_ROOT."/templates", ULICMS_ROOT."/content/templates");

// Änderungen in der Datenbank von 9.0.1
db_query ( "CREATE TABLE IF NOT EXISTS `" . tbname ( "mails" ) . "` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `headers` TEXT NOT NULL,
  `to` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` mediumtext NOT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;" );

db_query ( "CREATE TABLE IF NOT EXISTS `" . tbname ( "history" ) . "` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;" );

// Änderungen in der Datenbank von 9.0.2
db_query("ALTER TABLE ".tbname("log")." ADD COLUMN `referrer` varchar(255) DEFAULT NULL");

db_query("ALTER TABLE ".tbname("content")." ADD COLUMN `og_title` varchar(255) DEFAULT ''");
db_query("ALTER TABLE ".tbname("content")." ADD COLUMN `og_type` varchar(255) DEFAULT ''");
db_query("ALTER TABLE ".tbname("content")." ADD COLUMN `og_image` varchar(255) DEFAULT ''");
db_query("ALTER TABLE ".tbname("content")." ADD COLUMN `og_description` varchar(255) DEFAULT ''");

// comments Ordner weggelöschen, sofern er noch existiert, da dieser nicht mehr benötigt wird.
$comments_dir = ULICMS_ROOT . "/comments";

setconfig("og_type", "article");

if(is_dir($comments_dir)){
   @SureRemoveDir ( $comments_dir, true );
}

setconfig ( "db_schema_version", "9.0.2" );

// Patch Manager zurücksetzen
$pkg = new PackageManager ();
$pkg->truncateInstalledPatches ();

// @unlink ("update.php");
ulicms_redirect ( "admin/" );

