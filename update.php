<?php 
// Require config and init-script
require_once "cms-config.php";
require_once "init.php";

$db_schema_version = getconfig("db_schema_version");

if($db_schema_version === "6.1"){

  // Verbesserung der Systemsicherheit
  // Das Verschlüsselungsverfahren wurde von ungesalzenen MD5
  // auf gesalzenes SHA1 umgestellt
  db_query("ALTER TABLE ".tbname("admins")." ADD `old_encryption` Boolean Default 0;") or die(mysql_error());
  db_query("UPDATE ".tbname("admins"). " SET `old_encryption` = 1");
  
  setconfig("db_schema_version", "6.2");
  @unlink("update.php");
  
  header("Location: admin/");
  exit();
}





?>
