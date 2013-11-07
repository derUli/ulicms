<?php
class packageManager{
 private $package_source;
 function __construct() {
    $this->package_source = getconfig("pkg_src");
 }
 

 
public function getInstalledModules(){
     $module_folder = ULICMS_ROOT.DIRECTORY_SEPERATOR."modules".DIRECTORY_SEPERATOR;
    
    
     $available_modules = Array();
     $directory_content = scandir($module_folder);
    
     natcasesort($directory_content);
     for($i = 0;$i < count($directory_content);$i++){
         $module_init_file = $module_folder . $directory_content[$i] . "/" .
         $directory_content[$i] . "_main.php";
        
        
         if($directory_content[$i] != ".." and $directory_content[$i] != "."){
             if(is_file($module_init_file)){
                 array_push($available_modules, $directory_content[$i]);
                 }
             }
         }
     natcasesort($available_modules);
     return $available_modules;
} 

public function getInstalledThemes(){
    $themes = Array();
    $templateDir = ULICMS_ROOT.DIRECTORY_SEPERATOR."templates".DIRECTORY_SEPERATOR;
    
     $folders = scanDir($templateDir);
     natcasesort($folders);
     for($i = 0; $i < count($folders); $i++){
         $f = $templateDir . ($folders[$i]) . "/";
         if(is_dir($f)){
             if(is_file($f . "oben.php") and is_file($f . "unten.php")
                     and is_file($f . "style.css"))
                 array_push($themes, $folders[$i]);
            
             }
         }
    
     natcasesort($themes);
    
     return $themes;

}
 
public function getInstalledPackages($type = 'modules'){
 
   if($type === 'modules'){
      return $this->getInstalledModules();
   } else if($type === 'themes'){
      return $this->getInstalledThemes();
   } else {
      return null; 
   }
 
 
 }
 
public function getPackageSource(){
    return $this->package_source;
 }
 
public function setPackageSource($url){
    $this->package_source = $url;
 }
}