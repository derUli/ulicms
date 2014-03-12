<?php
include_once "init.php";

db_query("ALTER TABLE " . tbname("content") . " ADD COLUMN `html_file` VARCHAR(255) DEFAULT NULL");

setconfig("backend_style", "green");

db_query("ALTER TABLE  `". tbname("admins") ."` ADD  `notify_on_login` BOOLEAN NOT NULL DEFAULT FALSE AFTER  `group_id` ;");

db_query("CREATE TABLE IF NOT EXISTS `".tbname("custom_fields")."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");   

db_query("CREATE TABLE IF NOT EXISTS `".tbname("custom_fields_data")."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `content` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

setconfig("db_schema", "7.2");

$languages = getAllLanguages();
$old_frontpage = getconfig("frontpage");

for($p = 0; $p < count($languages); $p++){
     $lang = $languages[$p];
     setconfig("frontpage_" . $lang, $old_frontpage);
    }

setconfig("email_mode", "internal");

// unlink("update.php");
header("Location: admin/");
?>