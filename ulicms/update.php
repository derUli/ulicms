<?php
include_once "init.php";

$sql = "ALTER TABLE `" . tbname("banner"). "` ADD `type` VARCHAR(255) DEFAULT 'gif'";
db_query($sql);

$sql = "ALTER TABLE `" . tbname("banner"). "` ADD `html` TEXT DEFAULT ''";
db_query($sql);

setconfig("db_schema", "7.2.1");

// unlink("update.php");
header("Location: admin/");

setconfig("ckeditor_skin", "kama");
?>