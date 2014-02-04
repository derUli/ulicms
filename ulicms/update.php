<?php 
include_once "init.php";

db_query("ALTER TABLE ".tbname("content"). " ADD COLUMN `html_file` VARCHAR(255) DEFAULT NULL");

setconfig("backend_style", "green");

setconfig("db_schema", "7.1");

// unlink("update.php");
header("Location: admin/");
?>