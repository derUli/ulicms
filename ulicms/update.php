<?php
define("SKIP_TABLE_CHECK", true);
include_once "init.php";

db_query("ALTER TABLE " . tbname("content") . " ADD `theme` varchar(200) null");
db_query("ALTER TABLE " . tbname("categories") . " ADD `description` TEXT NULL DEFAULT ''");

setconfig("font-size", "medium");
setconfig('db_schema_version', '8.0.0');


//  @unlink ("update.php");
ulicms_redirect("admin/");
?>
