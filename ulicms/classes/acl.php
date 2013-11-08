<?php 
class ACL{

public function setPermission($name, $value, $group_id){

   $result = $this->getPermissionQueryResult();
   
   if(!$result)
      return false;
      
   // JSON holen
   $json = $result["permissions"];
   if(is_null($json) or strlen($json) < 2)
      return false;
      
   $permissionData = json_decode($json);
   
   $permissionData[$name] = $value;
   
   $newJSON = json_encode($permissionData);
   
   $updateSQLString = "UPDATE `".tbname("groups")."` SET `permissions`='".db_escape($newJSON)."' WHERE id=".$group_id;
    
}

public function hasPermission($name){

   $result = $this->getPermissionQueryResult();
   
   if(!$result)
      return false;
   
   // JSON holen
   $json = $result["permissions"];
   if(is_null($json) or strlen($json) < 2)
      return false;
      
   $permissionData = json_decode($json);
   if(!isset($permissionData[$name]))
      return false;
      
   if(is_null($permissionData[$name]))
      return false;
      
   return $permissionData[$name];
}

public function getPermissionQueryResult($id = null){
   if($id)
      $group_id = $id;
   else
      $group_id = $_SESSION["group_id"];
   if(!$group_id)
      return null;   
      
   
   $sqlString = "SELECT * FROM `".tbname("groups")."` WHERE id=".$group_id;
   $query = db_query($sqlString);
   
    if(mysql_num_rows($query) == 0)
      return null;
      
      
   $result = db_fetch_assoc($query);
   
   return $result;
}

public function getDefaultACLAsJSON($admin = false){
   $acl_data = Array();
   
   // Hook für das Erstellen eigener ACL Objekte
   // Temporäres globales Array zum hinzufügen eigener Objekte
   global $tmp_acl;
   $acl_array = $acl_data;
   add_hook("custom_acl");
   $acl_data = $acl_array;
   unset($acl_array);
   
   // Admin has all rights
   if($admin){
      foreach ($acl_data as $key => $value){
          $acl_data[$key] = true;
      } 
   } else {
      foreach ($acl_data as $key => $value){
          $acl_data[$key] = false;
      } 
   }
   return $acl_data;
}

}
