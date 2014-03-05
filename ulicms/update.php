<?php
include_once "init.php";

db_query("ALTER TABLE " . tbname("content") . " ADD COLUMN `html_file` VARCHAR(255) DEFAULT NULL");

setconfig("backend_style", "green");

setconfig("db_schema", "7.1");

$languages = getAllLanguages();
$old_frontpage = getconfig("frontpage");

for($p = 0; $p < count($languages); $p++){
     $lang = $languages[$p];
     setconfig("frontpage_" . $lang, $old_frontpage);
    }

setconfig("email_mode", "internal");

// unlink("update.php");
header("Location: admin/");
?>