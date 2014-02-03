<?php 
include_once "init.php";

db_query("ALTER TABLE ".tbname("content"). " ADD COLUMN `html_file` VARCHAR(255) DEFAULT NULL");

// unlink("update.php");
header("Location: admin/");
?>