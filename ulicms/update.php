<?php
include_once "init.php";

$sql = "ALTER TABLE `" . tbname("banner"). "` ADD `type` VARCHAR(255) DEFAULT 'gif'";
db_query($sql);

$sql = "ALTER TABLE `" . tbname("banner"). "` ADD `html` TEXT DEFAULT ''";
db_query($sql);

$sql = "ALTER TABLE `".tbname("languages")."` ADD UNIQUE(`language_code`)";
db_query($sql);

$sql = "ALTER TABLE `".tbname("content")."` MODIFY `autor` INT(11) NULL;");
db_query($sql);


$sql = "ALTER TABLE `".tbname("content")."` MODIFY `language`  varchar(6) NULL;");
db_query($sql);


$constraint1 = "ALTER TABLE `" .  tbname("admins")."` ADD FOREIGN KEY (`group_id`) REFERENCES `".tbname("groups"). "`(`id`) 
ON DELETE SET NULL";
 db_query($constraint1);
 
$constraint2 = "ALTER TABLE `" . tbname("content") ."` ADD FOREIGN KEY (`category`) REFERENCES `".tbname("categories") . "`(`id`) 
ON DELETE SET NULL";
 db_query($constraint2);


$constraint3 = "ALTER TABLE `" . tbname("banner") ."` ADD FOREIGN KEY (`category`) REFERENCES `".tbname("categories"). "`(`id`) 
ON DELETE SET NULL";
 db_query($constraint3);


$constraint4 = "ALTER TABLE `" . tbname("content") ."` ADD FOREIGN KEY (`autor`) REFERENCES `".tbname("admins") . "`(`id`) 
ON DELETE SET NULL";
 db_query($constraint4);

/*
$constraint5 = "ALTER TABLE `" . tbname("content") ."` ADD FOREIGN KEY (`language`) REFERENCES `".tbname("languages"). "`(`language_code`) 
ON DELETE SET NULL";
 db_query($constraint5);
*/

setconfig("db_schema", "7.2.1");
setconfig("ckeditor_skin", "moono");

// unlink("update.php");
header("Location: admin/");

?>