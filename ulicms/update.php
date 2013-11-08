<?php
include_once "init.php";
// neue Config-Variablen anlegen
setconfig("mailer", "php-mail");
setconfig("cache_type", "file");
setconfig("registered_user_default_level", "10");
setconfig("override_shortcuts", "backend");


$create_table_groups_sql = "CREATE TABLE IF NOT EXISTS `".tbname("groups")."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `permissions` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

$add_column_group_id = "ALTER TABLE `".tbname("admins")."` ADD `group_id` int NULL";

db_query($create_table_groups_sql);
db_query($add_column_group_id);


setconfig("db_schema_version", "6.7");

// Das Script versucht sich selbst zu löschen
@unlink("update.php");

// Zurück ins Backend
header ("Location: admin/");
exit();
?>