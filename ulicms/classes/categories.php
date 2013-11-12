<?php
class categories
{
  public static addCategory($name = null){
  if(is_null($name) or is_empty($name))
     return null;
    $sqlString = "INSERT INTO ".db_name_escape("categories")." (name) VALUES('".db_escape($name)."')":
    db_query($sqlString);
    return db_insert_id();
  }
  
  public static getAllCategories($order = 'id'){
     $sqlString = "SELECT * FROM ".db_name_escape("categories")." ORDER by ".$order;
     $result = db_query($sqlString);
     $arr = array();
     while($row = mysql_fetch_assoc($result)){
         array_push($arr, $row);
     
     }
     
     return $arr;
  }

}
