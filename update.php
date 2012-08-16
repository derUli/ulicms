<?php
require_once "init.php";

// Update Script von Version 4.5 auf Version 2012 R1

//Create deleted_at row, for recycle bin
mysql_query("ALTER TABLE `".$prefix."content` ADD `deleted_at` BIGINT NULL AFTER `access`"); 

mysql_query("CREATE TABLE `".$prefix."backend_menu_structure` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`action` VARCHAR( 100 ) NOT NULL ,
`label` VARCHAR( 100 ) NOT NULL ,
`position` INT NOT NULL
) ENGINE = MYISAM ;
");

@chmod("update.php", 0777);

// Update-Skript löscht sich selbst
@unlink("update.php");

// Redirect zum Dashboard
header("Location: admin/");

// Schluss
exit();

?>