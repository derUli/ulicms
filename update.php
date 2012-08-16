<?php
require_once "init.php";

// Update Script von Version 4.4 auf Version 4.5

$config = new config();
$prefix = $config->mysql_prefix;

// Konfigurationsvariable setzen
setconfig("visitors_can_register", "on");

// Änderungen an der Datenbank durchführen
mysql_query("UPDATE ".$prefix."admins SET password = MD5(password)");
mysql_query("ALTER TABLE `".$prefix."content` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ");
mysql_query("ALTER TABLE `".$prefix."news` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ");


mysql_query("ALTER TABLE `".$prefix."content` ADD `valid_from` DATE NOT NULL AFTER `parent` ,
ADD `valid_to` DATE NOT NULL AFTER `valid_from` ,
ADD `access` VARCHAR( 100 ) NOT NULL AFTER `valid_to`");

mysql_query("UPDATE ".$prefix."content SET valid_from = NOW(), access = 'all'");

@chmod("update.php", 0777);

// Update-Skript löscht sich selbst
@unlink("update.php");

// Redirect zum Dashboard
header("Location: admin/");

// Schluss
exit();

?>