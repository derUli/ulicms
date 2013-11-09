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

       
$insert_group_query = 'INSERT INTO `'.tbname("groups").'` (`id`, `name`, `permissions`) VALUES
(1, \'Administrator\', \'{"banners":true,"cache":true,"dashboard":true,"design":true,"expert_settings":true,"files":true,"flash":true,"groups":true,"images":true,"info":true,"install_packages":true,"languages":true,"list_packages":true,"logo":true,"module_settings":true,"motd":true,"other":true,"pages":true,"pkg_settings":true,"remove_packages":true,"settings_simple":true,"spam_filter":true,"templates":true,"update_system":true,"users":true}\')';




db_query($create_table_groups_sql)or die(db_error());
db_query($add_column_group_id)or die(db_error());

$set_group_id = "UPDATE ".tbname("admins"). " SET `group_id`=1 WHERE `group_id` = NULL";


db_query($insert_group_query)or die(db_error());
db_query($set_group_id)or die(db_error());

setconfig("db_schema_version", "6.7");

// Das Script versucht sich selbst zu löschen
@unlink("update.php");

// Zurück ins Backend
header ("Location: admin/");
exit();
?>