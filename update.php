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

mysql_query("INSERT INTO `".$prefix."news` (`id`, `title`, `content`, `date`, `active`, `autor`) VALUES (NULL, 'UliCMS 4.5 Entwicklerversion', '<p>Das hier ist die aktuelle Entwicklerversion von UliCMS 4.5.<br/>
Beachten Sie bitte, dass diese Software noch nicht 100-prozentig fertig ist und noch Fehler enthalten kann.</p>
<p>Lesen Sie bitte die news.txt und update.php um Informationen über diese Version zu bekommen.</p>', '1344084710', '1', '1');");

@chmod("update.php", 0777);

// Update-Skript löscht sich selbst
@unlink("update.php");

// Redirect zum Dashboard
header("Location: admin/");

// Schluss
exit();

?>