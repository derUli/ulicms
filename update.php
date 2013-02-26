<?php 
// Require config and init-script
require_once "cms-config.php";
require_once "init.php";
setconfig("spamfilter_enabled", "yes");
setconfig("country_blacklist", "");
setconfig("country_whitelist", "");


//@unlink("update.php");

header("Location: admin/");
exit();

?>