<?php
require_once "init.php";

// Update Script von Version 4.3 auf Version 4.4

$config = new config();
$prefix = $config->mysql_prefix;

// Nothing to do

setconfig("visitors_can_register", "on");

mysql_query("UPDATE ".$prefix."admins SET password = MD5(password)");

header("Location: admin/");


?>