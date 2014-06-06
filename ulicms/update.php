<?php
include_once "init.php";

if(getconfig("db_schema") == "7.1"){
   $sql = "ALTER TABLE `" . tbname("content"). "` ADD `alternate_title` VARCHAR(255) DEFAULT ''";
   db_query($sql);
}

setconfig("db_schema", "7.2");

// unlink("update.php");
header("Location: admin/");
?>