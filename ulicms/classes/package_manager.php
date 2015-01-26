<?php
class packageManager{
     private $package_source;
     public function __construct(){
         $cfg = new config();
         $this -> package_source = $cfg -> getVar("pkg_src");
         $this -> package_source = $this -> replacePlaceHolders($this -> package_source);
         }

      public function splitPackageName($name){
         $name = str_ireplace(".tar.gz", "", $name);
         $name = str_ireplace(".zip", "", $name); 
         $splitted = explode("-", $name);
         $version = array_pop($splitted);
         $name = $splitted;
         return array(join("-", $name), $version);

    }  
    
     public function installPackage($file){
         try{
             // Paket entpacken
            $phar = new PharData($file);
             $phar -> extractTo(ULICMS_ROOT, null, true);
            
             $post_install_script = ULICMS_ROOT . DIRECTORY_SEPARATOR . "post-install.php";
            
             // post_install_script ausführen und anschließend
            // entfernen, sofern vorhanden;
            if(file_exists($post_install_script)){
                 include_once $post_install_script;
                 unlink($post_install_script);
                 }
             return true;
             }
         catch (Exception $e){
             return false;
             }
        
         }
    
     private function replacePlaceHolders($url){
         $cfg = new config();
         $version = new ulicms_version();
         $internalVersion = $version -> getInternalVersion();
         $internalVersion = implode(".", $internalVersion);
         $url = str_replace("{version}", $internalVersion, $url);
         return $url;
         }
    
     public function getInstalledModules(){
         $module_folder = ULICMS_ROOT . DIRECTORY_SEPERATOR . "modules" . DIRECTORY_SEPERATOR;
        
        
         $available_modules = Array();
         $directory_content = scandir($module_folder);
        
         natcasesort($directory_content);
         for($i = 0;$i < count($directory_content);$i++){
             if(is_dir($module_folder . $directory_content[$i])){
                 $module_init_file = $module_folder . $directory_content[$i] . "/" .
                 $directory_content[$i] . "_main.php";
                
                
                 if($directory_content[$i] != ".." and $directory_content[$i] != "."){
                     if(is_file($module_init_file)){
                         array_push($available_modules, $directory_content[$i]);
                         }
                     }
                 }
            
             }
         natcasesort($available_modules);
         return $available_modules;
         }
    
     public function getInstalledThemes(){
         $themes = Array();
         $templateDir = ULICMS_ROOT . DIRECTORY_SEPERATOR . "templates" . DIRECTORY_SEPERATOR;
        
         $folders = scanDir($templateDir);
         natcasesort($folders);
         for($i = 0; $i < count($folders); $i++){
             $f = $templateDir . $folders[$i] . "/";
             if(is_dir($templateDir . $folders[$i])){
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
             return $this -> getInstalledModules();
             }else if($type === 'themes'){
             return $this -> getInstalledThemes();
             }else{
             return null;
             }
        
        
         }
    
     public function getPackageSource(){
         return $this -> package_source;
         }
    
     public function setPackageSource($url){
         $this -> package_source = $url;
         }
     }
