<?php
define ( "SKIP_TABLE_CHECK", true );
include_once "init.php";

@set_time_limit ( 0 );

// Move folders to content Folder
if (file_exists ( ULICMS_ROOT . "/modules" )) {
	@rename ( ULICMS_ROOT . "/modules", ULICMS_ROOT . "/content/modules" );
}
if (file_exists ( ULICMS_ROOT . "/templates" )) {
	@rename ( ULICMS_ROOT . "/templates", ULICMS_ROOT . "/content/templates" );
}

// Änderungen in der Datenbank von 9.0.1

Database::query ( "CREATE TABLE IF NOT EXISTS `" . tbname ( "mails" ) . "` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `headers` TEXT NOT NULL,
  `to` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` mediumtext NOT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;" );

Database::query ( "CREATE TABLE IF NOT EXISTS `" . tbname ( "history" ) . "` (
  `id` int(11) NOT NULL AUTO_INCREMENT AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;" );

// Änderungen in der Datenbank von 9.8.0

// Log
Database::query ( "ALTER TABLE " . tbname ( "log" ) . " ADD COLUMN `referrer` varchar(255) DEFAULT NULL" );

// Open Graph
Database::query ( "ALTER TABLE " . tbname ( "content" ) . " ADD COLUMN `og_title` varchar(255) DEFAULT ''" );
Database::query ( "ALTER TABLE " . tbname ( "content" ) . " ADD COLUMN `og_type` varchar(255) DEFAULT ''" );
Database::query ( "ALTER TABLE " . tbname ( "content" ) . " ADD COLUMN `og_image` varchar(255) DEFAULT ''" );
Database::query ( "ALTER TABLE " . tbname ( "content" ) . " ADD COLUMN `og_description` varchar(255) DEFAULT ''" );
setconfig ( "og_type", "article" );

// Forms Builder
Database::query ( "CREATE TABLE IF NOT EXISTS `" . tbname ( "forms" ) . "` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email_to` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `fields` text,
  `mail_from_field` varchar(255) NULL,
  `target_page_id` int(11) DEFAULT NULL,
  `created` bigint(20) DEFAULT NULL,
  `updated` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;" );

// Users Tabelle
Database::query ( "ALTER TABLE " . tbname ( "users" ) . " ADD COLUMN `locked` tinyint(1) NOT NULL DEFAULT '0'" );

// Last Login Time
Database::query ( "ALTER TABLE " . tbname ( "users" ) . " ADD COLUMN `last_login` bigint(20) DEFAULT NULL" );

// Failed login attempts Counter
Database::query ( "ALTER TABLE " . tbname ( "users" ) . " ADD COLUMN `failed_logins` int(11) DEFAULT '0'" );

// Database changes of 9.8.2

// New profile fields
Database::query ( "ALTER TABLE " . tbname ( "users" ) . " ADD COLUMN `twitter` varchar(15) NULL" );
Database::query ( "ALTER TABLE " . tbname ( "users" ) . " ADD COLUMN `homepage` TEXT NULL" );

// comments Ordner weggelöschen, sofern er noch existiert, da dieser nicht mehr benötigt wird.
$comments_dir = ULICMS_ROOT . "/comments";

if (is_dir ( $comments_dir )) {
	@SureRemoveDir ( $comments_dir, true );
}

// Delete .htaccess in content Folder
$content_htaccess = ULICMS_ROOT . "/content/.htaccess";
if (file_exists ( $content_htaccess )) {
	@unlink ( $content_htaccess );
}

// Database changes of 9.8.4

// Tabelle für Content Type "Liste"
Database::query ( "CREATE TABLE IF NOT EXISTS `" . tbname ( "lists" ) . "` (
  `content_id` int(11) NOT NULL,
  `language` varchar(50) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `menu` varchar(10) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `order_by` varchar(30) DEFAULT 'title',
  `order_direction` varchar(30) DEFAULT 'asc',
  `limit` int(11) default null,
  UNIQUE KEY `content_id` (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;" );

Database::query ( "alter table " . tbname ( "content" ) . " add column `module` varchar(200) default null" );
Database::query ( "alter table " . tbname ( "content" ) . " add column `video` int(11) default null" );
Database::query ( "alter table " . tbname ( "content" ) . " add column `audio` int(11) default null" );
Database::query ( "alter table " . tbname ( "content" ) . " add column `text_position` varchar(10) default 'before'" );
Database::query ( "ALTER TABLE " . tbname ( "content" ) . " ADD COLUMN `approved` tinyint(1) NOT NULL DEFAULT '1'" );

Database::query ( "update " . tbname ( "content" ) . " set `type` = 'link' where redirection is not null and redirection <> ''" );

Database::query ( "alter table " . tbname ( "content" ) . "  add column `image_url` text default null" );

Settings::set ( "db_schema_version", "9.8.4" );

// Patch Manager zurücksetzen
$pkg = new PackageManager ();
$pkg->truncateInstalledPatches ();

// @unlink ("update.php");
Request::redirect ( "admin/" );
