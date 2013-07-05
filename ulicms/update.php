<?php 
// Require config and init-script
require_once "cms-config.php";
require_once "init.php";

$db_schema_version = getconfig("db_schema_version");

if($db_schema_version === "6.3")
   die("Das Update wurde bereits installiert!");

  setconfig("theme", "default");
  setconfig("db_schema_version", "6.3");
  @unlink("update.php");
  
  header("Location: admin/");
  exit();






?>