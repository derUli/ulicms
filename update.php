<?php 
// Require config and init-script
require_once "cms-config.php";
require_once "init.php";
setconfig("spamfilter_enabled", "yes");
setconfig("country_blacklist", "cn,br,ru");
setconfig("country_whitelist", "de,ch,at");


//@unlink("update.php");

header("Location: admin/");


?>