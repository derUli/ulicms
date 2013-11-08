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
    
    
   // Willkommen
   $acl_data["dashboard"] = null;
   
   // Inhalte
   $acl_data["pages"] = null;
   $acl_data["banners"] = null;
   
   // Medien
   $acl_data["images"] = null;
   $acl_data["files"] = null;
   $acl_data["flash"] = null;
   
   // Benutzer
   $acl_data["user"] = null;
   
   // Templates
   $acl_data["templates"] = null;
   
   // Package Manager
   $acl_data["list_packages"] = null;
   $acl_data["install_packages"] = null;
   $acl_data["remove_packages"] = null;
   
   // Updates durchführen
   $acl_data["update_system"] = null;
   
   // Einstellungen
   $acl_data["settings_simple"] = null;
   $acl_data["design"] = null;
   $acl_data["spam_filter"] = null;
   $acl_data["cache"] = null;
   $acl_data["motd"] = null;
   $acl_data["pkg_settings"] = null;
   $acl_data["languages"] = null;
   $acl_data["logo"] = null;
   $acl_data["other"] = null;
   $acl_data["expert_settings"] = null;
   
   // Hook für das Erstellen eigener ACL Objekte
   // Temporäres globales Array zum hinzufügen eigener Objekte
   global $tmp_acl;
   $acl_array = $acl_data;
   add_hook("custom_acl");
   $acl_data = $acl_array;
   unset($acl_array);

      // Admin has all rights
   if($admin)
      $default_value = true;
   else
      $default_value = false;
  
      foreach ($acl_data as $key => $value){
          $acl_data[$key] = $default_value;
      }
   $json = json_encode($acl_data);
   return $json;
}

}
