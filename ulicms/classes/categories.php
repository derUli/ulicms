<?php
class categories
{

  public static function updateCategory($id, $name){
     $sql = "UPDATE ".tbname("categories")." SET name='".db_escape($name)."' WHERE id=".$id;
     return db_query($sql);
  }

  public static function addCategory($name = null){
   if(is_null($name) or empty($name))
      return null;
    $sqlString = "INSERT INTO ".tbname("categories")." (name) VALUES('".db_escape($name)."')";
    db_query($sqlString);
    return db_insert_id();
  }
  
  public static function getHTMLSelect($default = 1){
      $lst = self::getAllCategories("name");
      $html = "<select name='category' size='1'>";
      foreach($lst as $cat){
        if($cat["id"] == "default")
           $html .= "<option value='".$cat["id"]."' selected='selected'>".db_escape($cat["name"])."</option>";
        else
           $html .= "<option value='".$cat["id"]."'>".db_escape($cat["name"])."</option>";
      }
      
      $html .="</select>";
      return $html;
  }
  
  public static function deleteCategory($id){
     $sqlString = "DELETE FROM ".tbname("categories").
     " WHERE id = ".$id;
     return db_query($sqlString);
  }
  
  public static function getCategoryById($id){
       $sqlString = "SELECT * FROM ".tbname("categories")." WHERE id=".$id;
       $result = db_query($sqlString);
       if(db_num_rows($result) > 0){
          $row = db_fetch_assoc($result);
 
          return $row["name"];
          }
          
          return null;
          
  }
  
  public static function getAllCategories($order = 'id'){
     $sqlString = "SELECT * FROM ".tbname("categories")." ORDER by ".$order;
     $result = db_query($sqlString);
     $arr = array();
     while($row = db_fetch_assoc($result)){
         array_push($arr, $row);
     }
     
     return $arr;
  }

}
