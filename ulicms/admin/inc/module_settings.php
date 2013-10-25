<?php
if(!isset($_SESSION["group"])){
     die("Nicht erlaubter Zugriff");
     }

$module = basename($_GET["module"]);

$admin_file_path = getModuleAdminFilePath($module);

if(!file_exists($admin_file_path)){
     ?>
<p class='ulicms_error'>Dieses Modul bietet keine Einstellungen.</p>
<?php }else{
    
     include $admin_file_path;
    
     if(defined("MODULE_ADMIN_HEADLINE")){
         echo "<h1>" . MODULE_ADMIN_HEADLINE . "</h1>";
         }
    else{
         $capitalized_module_name = ucwords($module);
         echo "<h1>$capitalized_module_name  Einstellungen</h1>";
         }
    
     if(defined("MODULE_ADMIN_REQUIRED_PERMISSION")){
         if($_SESSION["group"] >= MODULE_ADMIN_REQUIRED_PERMISSION){
             define("MODULE_ACCESS_PERMITTED", true);
             }
        else{
             define("MODULE_ACCESS_PERMITTED", false);
             }
         }
    else{
         define("MODULE_ACCESS_PERMITTED", true);
         }
    
     $admin_func = $module . "_admin";
    
     if(function_exists($admin_func)){
         if(MODULE_ACCESS_PERMITTED){
             call_user_func($admin_func);
             }
        else{
             echo "<p>Zugriff verweigert</p>";
             }
         }else{
         echo "<p>Keine Einstellungsmöglichkeiten vorhanden.</p>";
        
         }
    
     }
?>
<?php }?>