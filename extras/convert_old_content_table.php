<?php
include_once "init.php";
$old_table = "old_table";
$new_table = "new_table";

$old_content = db_query("SELECT * FROM $old_table");
while($row = db_fetch_assoc($old_content)){
  
  foreach($row as $key=>$value){
      if($key == "parent"){
         if(!is_null($value) and !empty($value)){
         $sub_query = db_query("select id from $old_table where systemname='".db_escape($value)."'");
         $result = db_fetch_assoc($sub_query);
         $row["parent"] = $result["id"];
        } else {
          $row["parent"] = "NULL";
        } 
         }
         
  }
  
  db_query("INSERT INTO `".$new_table."` (id, notinfeed, systemname, title, content, active,
created, lastchangeby, autor, views, comments_enabled, redirection, menu, position, parent, lastmodified)

VALUES (".$row["id"].",0, '".db_escape(utf8_decode($row["systemname"]))."', 
'".db_escape($row["title"])."', 
".db_escape($row["content"])."',
 1, ".time().",
1, 1, 0, 0, 
'', '".db_escape($row["menu"])."', 
".$row["position"].", ".$row["parent"].", ".time().")");
}