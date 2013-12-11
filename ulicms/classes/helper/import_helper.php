<?php
class ImportHelper{
static function importJSON($target, $json){
      $data = json_decode($json);
      for($i=0; $i < count($data); $i++){
          $ds = $data[$i];
          if($ds["id"]){
             $id = intval($ds["id"]);
          } else {
             $id = 0;
          }
          
          foreach($row as $key=>$value){
           if($key != "id" and $id > 0){
              $query = db_query("SELECT * FROM ".$target. " WHERE id=".$id);
              if(mysql_num_rows($query) > 0){
              db_query("UPDATE ".$target." SET `".$key."` = '".db_escape($value)."' WHERE id = $id");
              }
            }
          
          }
          
      }
    
    
     }
     
     }

