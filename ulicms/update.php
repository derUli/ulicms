<?php
define("SKIP_TABLE_CHECK", true);
include_once "init.php";

// Move folders to content Folder
if(file_exists(ULICMS_ROOT."/modules")){
  @rename(ULICMS_ROOT."/modules", ULICMS_ROOT."/content/modules");
}
if(file_exists(ULICMS_ROOT."/templates")){
  @rename(ULICMS_ROOT."/templates", ULICMS_ROOT."/content/templates");
}

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
setconfig("og_type", "article");

// Forms Builder


db_query("CREATE TABLE IF NOT EXISTS `".tbname("forms")."` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email_to` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `fields` text,
  `target_page_id` int(11) DEFAULT NULL,
  `created` bigint(20) DEFAULT NULL,
  `updated` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

// comments Ordner weggelöschen, sofern er noch existiert, da dieser nicht mehr benötigt wird.
$comments_dir = ULICMS_ROOT . "/comments";

if(is_dir($comments_dir)){
   @SureRemoveDir ( $comments_dir, true );
}

// Delete .htaccess in content Folder
$content_htaccess = ULICMS_ROOT."/content/.htaccess";
if(file_exists($content_htaccess)){
   @unlink($content_htaccess);
}

setconfig ( "db_schema_version", "9.0.2" );

// Patch Manager zurücksetzen
$pkg = new PackageManager ();
$pkg->truncateInstalledPatches ();


// @unlink ("update.php");

if(!isset($_GET["include_update"])){
   ulicms_redirect ( "admin/" );
}
