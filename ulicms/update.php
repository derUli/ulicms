<?php
define("SKIP_TABLE_CHECK", true);
include_once "init.php";

db_query("ALTER TABLE ".tbname("content")." ADD `theme` varchar(200) null");
db_query("ALTER TABLE " . tbname("categories") . " ADD `description` varchar(255) NULL DEFAULT ''");

// @unlink("update.php");
ulicms_redirect("admin/");
?>
