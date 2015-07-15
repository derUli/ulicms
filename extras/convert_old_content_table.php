<?php
// Copy content from a very ancient UliCMS version into a moderner one
// Converts old Parent row format (varchar) to new one (int ID)

include_once "init.php";
$old_table = "old_cotent";
$new_table = "new_content";

$old_content = db_query("SELECT * FROM $old_table");
while($row = db_fetch_assoc($old_content)){
  
  foreach($row as $key=>$value){
      if($key == "parent"){
         if(!is_null($value) and !empty($value) and $value != "-"){
         $sub_query = db_query("select id from $old_table where systemname='".db_escape($value)."'");
         $result = db_fetch_assoc($sub_query);
         $row["parent"] = $result["id"];
        } else {
          $row["parent"] = "NULL";
        } 
         }
         
  }
  
  db_query("INSERT INTO `".$new_table."` (id, notinfeed, systemname, title, content, active,
created, lastchangeby, autor, views, comments_enabled, redirection, menu, position, parent, lastmodified, language)

VALUES (".$row["id"].",0, '".db_escape(utf8_decode($row["systemname"]))."', 
'".db_escape(utf8_decode($row["title"]))."', 
'".db_escape(utf8_decode($row["content"]))."',
 1, ".time().",
1, 1, 0, 0, 
'', '".db_escape($row["menu"])."', 
".$row["position"].", ".$row["parent"].", ".time().", 'de')")or die (db_error());
}