<?php
class ACL{
     public function setPermission($name, $value, $group_id){
         $result = $this -> getPermissionQueryResult ();
        
         if (! $result)
             return false;
        
         // JSON holen
        $json = $result ["permissions"];
         if (is_null ($json) or strlen ($json) < 2)
             return false;
        
         $permissionData = json_decode ($json, true);
        
         $permissionData [$name] = $value;
        
         $newJSON = json_encode ($permissionData);
        
         $updateSQLString = "UPDATE `" . tbname ("groups") . "` SET `permissions`='" . db_escape ($newJSON) . "' WHERE id=" . $group_id;
         }
     public function hasPermission($name){
         if (is_admin ())
             return true;
         $result = $this -> getPermissionQueryResult ();
         if (! $result)
             return false;
        
         // JSON holen
        $json = $result ["permissions"];
         if (is_null ($json) or strlen ($json) < 2)
             return false;
        
         $permissionData = json_decode ($json, true);
         if (! isset ($permissionData [$name]))
             return false;
        
         if (is_null ($permissionData [$name]))
             return false;
        
         return $permissionData [$name];
         }
     public function createGroup($name, $permissions = null){
         if (is_null ($permissions))
             $permission_data = $this -> getDefaultACL ();
         else
             $permissionData = json_encode ($permissions);
        
         $sql = "INSERT INTO `" . tbname ("groups") . "` (`name`, `permissions`) " . "VALUES('" . db_escape ($name) . "','" . db_escape ($permissionData) . "')";
        
         // Führe Query aus
        db_query ($sql);
        
         // Gebe die letzte Insert-ID zurück, damit man gleich mit der erzeugten Gruppe arbeiten kann.
        return db_last_insert_id ();
         }
     public function updateGroup($id, $name, $permissions = null){
         if (is_null ($permissions))
             $permission_data = $this -> getDefaultACL ();
         else
             $permissionData = json_encode ($permissions);
        
         $sql = "UPDATE `" . tbname ("groups") . "` SET name='" . db_escape ($name) . "', permissions='" . db_escape ($permissions) . "' WHERE id=" . $id;
        
         // Führe Query aus
        db_query ($sql);
        
         // Gebe die letzte Insert-ID zurück, damit man gleich mit der erzeugten Gruppe arbeiten kann.
        return $id;
         }
     public function deleteGroup($id, $move_users_to = null){
         $id = intval ($id);
         $deleteGroupSQL = "DELETE FROM `" . tbname ("groups") . "` WHERE id=" . $id;
         db_query ($deleteGroupSQL);
        
         if (is_null ($move_users_to))
             $updateUsers = "UPDATE " . tbname ("users") . " SET `group_id`=NULL where `group_id`=$id";
         else
             $updateUsers = "UPDATE " . tbname ("users") . " SET `group_id`=" . $move_users_to . " where `group_id`=$id";
        
         db_query ($updateUsers);
         }
     public function getPermissionQueryResult($id = null){
         if ($id)
             $group_id = $id;
         else
             $group_id = $_SESSION ["group_id"];
         if (! $group_id)
             return null;
        
         $sqlString = "SELECT * FROM `" . tbname ("groups") . "` WHERE id=" . $group_id;
         $query = db_query ($sqlString);
        
         if (db_num_rows ($query) == 0)
             return null;
        
         $result = db_fetch_assoc ($query);
        
         return $result;
         }
     public function getAllGroups($order = 'id DESC'){
         $sql = "SELECT * FROM `" . tbname ("groups") . "` ORDER by " . $order;
         $query = db_query ($sql);
         $list = array ();
         while ($assoc = db_fetch_assoc ($query)){
             $list [$assoc ["id"]] = $assoc ["name"];
             }
         return $list;
         }
     public function getDefaultACLAsJSON($admin = false, $plain = false){
         $acl_data = Array ();
        
         // Willkommen
        $acl_data ["dashboard"] = null;
        
         // Inhalte
        $acl_data ["pages"] = null;
         $acl_data ["banners"] = null;
         $acl_data ["categories"] = null;
        
         // Medien
        $acl_data ["images"] = null;
         $acl_data ["files"] = null;
         $acl_data ["videos"] = null;
         $acl_data ["audio"] = null;
        
         // Benutzer
        $acl_data ["users"] = null;
         $acl_data ["groups"] = null;
        
         // Templates
        $acl_data ["templates"] = null;
        
         // Package Manager
        $acl_data ["list_packages"] = null;
         $acl_data ["install_packages"] = null;
         $acl_data ["remove_packages"] = null;
         $acl_data ["module_settings"] = null;
        
         // Updates durchführen
        $acl_data ["update_system"] = null;
        
         // Einstellungen
        $acl_data ["settings_simple"] = null;
         $acl_data ["design"] = null;
         $acl_data ["spam_filter"] = null;
         $acl_data ["cache"] = null;
         $acl_data ["motd"] = null;
         $acl_data ["pkg_settings"] = null;
         $acl_data ["languages"] = null;
         $acl_data ["logo"] = null;
         $acl_data ["other"] = null;
         $acl_data ["expert_settings"] = null;
        
         $acl_data ["export"] = null;
         $acl_data ["import"] = null;
        
         $acl_data ["info"] = null;
        
         // Hook für das Erstellen eigener ACL Objekte
        // Temporäres globales Array zum hinzufügen eigener Objekte
        global $acl_array;
         $acl_array = $acl_data;
         add_hook ("custom_acl");
         $acl_data = $acl_array;
         unset ($acl_array);
        
         // Admin has all rights
        if ($admin)
             $default_value = true;
         else
             $default_value = false;
        
         foreach ($acl_data as $key => $value){
             $acl_data [$key] = $default_value;
             }
        
         ksort ($acl_data);
         if ($plain)
             return $acl_data;
        
         $json = json_encode ($acl_data);
         return $json;
         }
     public function getDefaultACL($admin = false, $plain = false){
         return $this -> getDefaultACLAsJSON ($admin, $plain);
         }
    }
