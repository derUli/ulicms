<?php
class categories
{
  public static function addCategory($name = null){
  if(is_null($name) or is_empty($name))
      return null;
    $sqlString = "INSERT INTO ".tbname("categories")." (name) VALUES('".db_escape($name)."')";
    db_query($sqlString);
    return db_insert_id();
  }
  
  public static function deleteCategory($id){
     $sqlString = "DELETE FROM ".tbname("categories").
     " WHERE id = ".$id;
     return db_query($sqlString);
  }
  
  public static function getAllCategories($order = 'id'){
     $sqlString = "SELECT * FROM ".db_name_escape("categories")." ORDER by ".$order;
     $result = db_query($sqlString);
     $arr = array();
     while($row = mysql_fetch_assoc($result)){
         array_push($arr, $row);
     
     }
     
     return $arr;
  }

}
