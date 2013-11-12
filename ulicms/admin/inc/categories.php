<?php
$acl = new ACL();
if(!is_admin() and !$acl -> hasPermission("groups")){
     noperms();
    
    }else{
    include_once "../lib/string_functions.php";
    
    
    $categories = categories::getAllCategories();
    
    
    
    
    
}
