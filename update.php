<?php
require_once "init.php";

$config = new config();
$prefix = $config->mysql_prefix;

mysql_query("ALTER TABLE  `".$prefix."content` ADD  `parent` VARCHAR( 300 ) NOT NULL AFTER  `position`");
mysql_query("UPDATE `".$prefix."content` SET parent='-'");

header("Location: admin/");

?>