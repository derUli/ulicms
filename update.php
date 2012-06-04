<?php
require_once "init.php";

// Update Script von Version 4.3 auf Version 4.4

$config = new config();
$prefix = $config->mysql_prefix;

// Nothing to do

header("Location: admin/");

?>