<?php
require_once "init.php";

// Update Script von Version 4.5 auf 4.6

//Create deleted_at row, for recycle bin
mysql_query("ALTER TABLE `".$prefix."content` ADD `deleted_at` BIGINT NULL AFTER `access`"); 


// Add Backend Menu Structure Database Table
mysql_query("CREATE TABLE `".$prefix."backend_menu_structure` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`action` VARCHAR( 100 ) NOT NULL ,
`label` VARCHAR( 100 ) NOT NULL ,
`position` INT NOT NULL
) ENGINE = MYISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;
");

// Create Database Structure
mysql_query("INSERT INTO `".$prefix."backend_menu_structure` (`id`, `action`, `label`, `position`) VALUES
(15, 'media', 'Medien', 3),
(14, 'home', 'Willkommen', 1),
(12, 'destroy', 'Logout', 10),
(24, 'system_update', 'Update', 7),
(23, 'contents', 'Inhalte', 2),
(18, 'templates', 'Templates', 5),
(19, 'info', 'Info', 9),
(20, 'settings_categories', 'Einstellungen', 8),
(21, 'modules', 'Module', 6),
(22, 'admins', 'Benutzer', 4);");

mysql_query("ALTER TABLE `".$prefix."admins` ADD `skype_id` VARCHAR( 32 ) NOT NULL AFTER `group` ,
ADD `icq_id` VARCHAR( 20 ) NOT NULL AFTER `skype_id` ,
ADD `avatar_file` VARCHAR( 40 ) NOT NULL AFTER `icq_id` ,
ADD `about_me` TEXT NOT NULL AFTER `avatar_file`");

@chmod("update.php", 0777);

// Update-Skript löscht sich selbst
@unlink("update.php");

// Redirect zum Dashboard
header("Location: admin/");

// Schluss
exit();

?>