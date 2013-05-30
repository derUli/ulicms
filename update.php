<?php 
// Require config and init-script
require_once "cms-config.php";
require_once "init.php";

$db_schema_version = getconfig("db_schema_version");

if(!$db_schema_version or $db_schema_version === "6.0"){
mysql_query("UPDATE ".tbname("content"). " SET menu='bottom' WHERE menu='down'");

setconfig("spamfilter_words_blacklist", 
"Casino||Euro Dice||Bingo||Cialis||Viagra||Penis||Enlargement||Drugstore");

setconfig("empty_trash_days", 30);


$tpl_unten_filename = "templates/unten.php";

if(file_exists($tpl_unten_filename) and is_writable($tpl_unten_filename)){

   $template_unten = file_get_contents($tpl_unten_filename);
   $template_unten = str_replace('menu("down")', 
   'menu("bottom")' , $template_unten);
   $template_unten = str_replace("menu('down')", 
   "menu('bottom')" , $template_unten);
   $template_unten = str_replace("comments()", "", $template_unten);
   
   $handle = fopen($tpl_unten_filename, "w");
   fwrite($handle, $template_unten);
   fclose($handle);
   
}

mysql_query("alter table ".tbname("content")." change `parent` `parent_old` varchar (300);");
mysql_query("ALTER TABLE ".tbname("content")." ADD parent int(11);");

$query = mysql_query("SELECT id, systemname, parent_old FROM ".tbname("content"));

while($row=mysql_fetch_object($query)){
   
   if($row->parent_old == "-"){
      mysql_query("UPDATE ".tbname("content"). " set parent = NULL where id=".$row->id);
   } else {
     $query2 = mysql_query("SELECT id FROM ".tbname("content")." WHERE systemname='".mysql_real_escape_string($row->parent_old)."'");
     
     if(mysql_num_rows($query2) > 0){
        $results = mysql_fetch_object($query2);
        mysql_query("UPDATE ".tbname("content"). " set parent=".$results->id." where id=".$row->id);
     } else {
        mysql_query("UPDATE ".tbname("content"). " set parent = NULL where id=".$row->id);
     }
     
     }
}

// Version des Datenbank-Schemas setzen
setconfig("db_schema_version", "6.1");

} else if($db_schema_version === "6.1"){

  // Verbesserung der Systemsicherheit
  // Das Verschlüsselungsverfahren wurde von ungesalzenen MD5
  // auf gesalzenes SHA1 umgestellt
  mysql_query("ALTER TABLE ".tbname("admins")." ADD `old_encryption` Boolean Default 0;") or die(mysql_error());
  mysql_query("UPDATE ".tbname("admins"). " SET `old_encryption` = 1");
  
  setconfig("db_schema_version", "6.2");
  
}

//@unlink("update.php");

header("Location: admin/");
exit();

?>
