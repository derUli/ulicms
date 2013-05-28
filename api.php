<?php

// get a config variable
function getconfig($key){
	$connection=MYSQL_CONNECTION;
	$ikey=mysql_real_escape_string($key);
	$query=mysql_query("SELECT * FROM ".tbname("settings")." WHERE name='$key'");
	if(mysql_num_rows($query)>0){
		while($row=mysql_fetch_object($query)){
		   return $row->value;
		}
	}
	else{
		return false;
	}
}


function checkForUpdates(){
  $info = @file_get_contents(UPDATE_CHECK_URL);
  
  if(!$info or trim($info) === "")
     return false;  
  else
     return $info;

}

function getModuleAdminSelfPath(){
    $self_path = $_SERVER["REQUEST_URI"];
    $self_path = str_replace('"', '', $self_path);
    $self_path = str_replace("'", '', $self_path);
    
    return $self_path;
}


function buildCacheFilePath($request_uri){
   $language = $_SESSION["language"];
   if(!$language){
     $language = getconfig("default_language");
   }
   
   $unique_identifier = $request_uri.$language;
   
   return "content/cache/".md5($unique_identifier).".html";
}


function SureRemoveDir($dir, $DeleteMe) {
    if(!$dh = @opendir($dir)) return;
    while (false !== ($obj = readdir($dh))) {
        if($obj=='.' || $obj=='..') continue;
        if (!@unlink($dir.'/'.$obj)) SureRemoveDir($dir.'/'.$obj, true);
    }

    closedir($dh);
    if ($DeleteMe){
        @rmdir($dir);
    }
}


/*
  Generate path to Page
  Argumente
  String $page (Systemname)
  Rückgabewert String im Format
  ../seite.html
  bzw.
  seite.html;
*/


function buildSEOUrl($page = false){
   if($page === false)
      $page = get_requested_pagename();

   
   $seo_url = "";
   
   if(is_file("backend.php"))
      $seo_url .= "../";
      

   $seo_url .= $page;
   $seo_url .= ".html";
   
   return $seo_url;

}

function getModulePath($module){
	// Frontend Directory
	if(is_file("cms-config.php")){
		$module_folder = "modules/";
	}
	// Backend Directory
	else{
		$module_folder = "../modules/";
	}
	$available_modules = Array();
	return $module_folder.$module."/";
}

function getModuleAdminFilePath($module){
	return getModulePath($module).
		$module."_admin.php";		
}

function getModuleMainFilePath($module){
	return getModulePath($module).
		$module."_main.php";
		
}



function convertLineEndingsToLF($s) {
    // Normalize line endings using Global
    // Convert all line-endings to UNIX format
    $s = str_replace(CRLF, LF, $s);
    $s = str_replace(CR, LF, $s);
    // Don't allow out-of-control blank lines
    $s = preg_replace("/\n{2,}/", LF . LF, $s);
    return $s;
}


function getAllModules(){
	// Frontend Directory
	if(is_file("cms-config.php")){
		$module_folder = "modules/";
	}
	// Backend Directory
	else{
		$module_folder = "../modules/";
	}
	
	
	$available_modules = Array();
	$directory_content = scandir($module_folder);
	sort($directory_content);
	for($i=0;$i<count($directory_content);$i++){
		$module_init_file=$module_folder.$directory_content[$i]."/".
		$directory_content[$i]."_main.php";

		
		if($directory_content[$i]!=".." and $directory_content[$i]!="."){
			if(is_file($module_init_file)){
				array_push($available_modules, $directory_content[$i]);
			}
		}
	}
	return $available_modules;
	
}


// replace Shortcodes with modules
function replaceShortcodesWithModules($string){
	$allModules = getAllModules();
	for($i=0;$i<=count($allModules);$i++){
		$thisModule = $allModules[$i];
		$stringToReplace1 = '[module="'.$thisModule.'"]';
		$stringToReplace2 = '[module=&quot;'.$thisModule.'&quot;]';
		
		$module_mainfile_path = getModuleMainFilePath($thisModule);
		
		if(is_file($module_mainfile_path) and (strstr($string, $stringToReplace1) or strstr($string, $stringToReplace2))){
			require_once $module_mainfile_path;
			if(function_exists($thisModule."_render")){
				$html_output = call_user_func($thisModule."_render");
			}
			else{
				$html_output = "<p class='ulicms_error'>Das Modul ".$thisModule.
				" konnte nicht geladen werden.</p>";
			}
			
		}
		else{
				$html_output = "<p class='ulicms_error'>Das Modul ".$thisModule.
				" konnte nicht geladen werden.</p>";
		}
		
		$string = str_replace($stringToReplace1, $html_output, $string);
		$string = str_replace($stringToReplace2, $html_output, $string);

	}
	return $string;
}


// get page id by systemname
function getPageIDBySystemname($systemname){
	$query = mysql_query("SELECT systemname, id FROM `".tbname("content")."` where systemname='".mysql_real_escape_string($systemname)."'");
	if(mysql_num_rows($query ) > 0){
	   $row = mysql_fetch_object($query);
	   return $row->id;
	} else {
	return null;
	}
}


// get PageSystemnameByID
function getPageSystemnameByID($id){
	$query = mysql_query("SELECT systemname, id FROM `".tbname("content")."` where id=".intval($id));
	if(mysql_num_rows($query ) > 0){
	   $row = mysql_fetch_object($query);
	   return $row->systemname;
	} else {
	return "-";
	}
}

// Get systemnames of all pages
function getAllSystemNames(){
	$query = mysql_query("SELECT systemname,id FROM `".tbname("content")."` WHERE `deleted_at`IS NULL ORDER BY systemname");
	$returnvalues = Array();
	while($row = mysql_fetch_object($query)){
		array_push($returnvalues, $row->systemname);
}

return $returnvalues;

}


// Sprachcodes abfragen und als Array zurück geben
function getAllLanguages(){
	$query = mysql_query("SELECT * FROM `".tbname("languages")."` ORDER BY language_code");
	$returnvalues = Array();
	while($row = mysql_fetch_object($query)){
		array_push($returnvalues, $row->language_code);
  }
        return $returnvalues;


       }

// get all menu items
function getAllMenuItems(){
	$query = mysql_query("SELECT * FROM `".tbname("backend_menu_structure")."` ORDER BY position ASC");
	$returnvalues = Array();
	while($row = mysql_fetch_assoc($query)){
		array_push($returnvalues, $row);
}

return $returnvalues;

}


//get URL to UliCMS
function the_url(){
	
	$path=getcwd();
	if(!is_file("cms-config.php")){
		chdir("..");
	}
	return "http://".$_SERVER["HTTP_HOST"].dirname($_SERVER["SCRIPT_NAME"]);
	chdir($path);

}


function file_extension($filename) {
        $ext = explode(".", $filename);
	$ext = end($ext);
	return $ext;
}

// Remove an configuration variable
function deleteconfig($key){
   $key = mysql_real_escape_string($key);
   mysql_query("DELETE FROM ".tbname("settings").
               " WHERE name='$key'");
   return mysql_affected_rows() > 0;
}

//Set a configuration Variable;
function setconfig($key, $value){
  $query=mysql_query("SELECT * FROM ".tbname("settings")." WHERE name='$key'");

  if(mysql_num_rows($query)>0){
	
    mysql_query("UPDATE ".tbname("settings")." SET value='$value' WHERE name='$key'");
  }else{
  
    mysql_query("INSERT INTO ".tbname("settings"). " (name, value) VALUES('$key', '$value')");
  }

}


function is__writable($path)
{

    if ($path{strlen($path)-1}=='/')
        
        return is__writable($path.uniqid(mt_rand()).'.tmp');
    
    elseif (file_exists($path) && preg_match('/\.tmp/', $path))
    {
        
        if (!($f = @fopen($path, 'w+')))
            return false;
        fclose($f);
        unlink($path);
        return true;

    }
    else
        
        return 0; // Or return error - invalid path...

}




// Check if site contains a module
function containsModule($page, $module = false){
   $query = mysql_query("SELECT * FROM ".tbname("content"). " WHERE systemname = '".
   mysql_real_escape_string($page)."'");
   $dataset = mysql_fetch_assoc($query);
   $content = $dataset["content"];
   $content = str_replace("&quot;", "\"", $content);
   
   if($module)  
      return preg_match("/\[module=\"".preg_quote($module)."\"\]/", 
      $content);
   else
      return preg_match("/\[module=\".+\"\]/", 
      $content);
   
}


// Ist der User eingeloggt
function is_logged_in(){
   return isset($_SESSION["group"]);
}

// Hat der Nutzer die notwendige Berechtigung
function has_permissions($mod){
   if(!isset($_SESSION["group"]))
      return false;
   
   return $_SESSION["group"] >= $mod;
}

// Alias für is_logged_in
function logged_in(){
   return is_logged_in();
}

// Tabellenname zusammensetzen
function tbname($name){
  require_once "cms-config.php";
  $config = new config();
  return $config->mysql_prefix.$name;
}

//returns version number of UliCMS Core
function cms_version(){
  require_once "version.php";
  $v = new ulicms_version();
  return $v->getVersion();
}


function is_admin(){
   return has_permissions(50);
}

require_once "users_api.php";
require_once "legacy.php";


?>
