<?php 
class ACL{

public function getDefaultACLAsJSON($admin = false){
   $acl_data = Array();
   
   // Admin has all rights
   if($admin){
      foreach ($acl_data as $key => $value){
          $acl_data[$key] = true;
      } 
   }
   return $acl_data;
}

}
