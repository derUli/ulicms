<?php

// get a config variable
function getconfig($key){
	$connection=MYSQL_CONNECTION;
	$ikey=mysql_real_escape_string($ikey);
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



function buildCacheFilePath($page){
   return "content/cache/".$page.".html";
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

// Get systemnames of all pages
function getAllSystemNames(){
	$query = mysql_query("SELECT * FROM `".tbname("content")."` ORDER BY systemname");
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
	return end(explode(".", $filename));
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
function containsModule($page){
   $query = mysql_query("SELECT * FROM ".tbname("content"). " WHERE systemname = '".
   mysql_real_escape_string($page)."'");
   $dataset = mysql_fetch_assoc($query);
   $content = $dataset["content"];
   $content = str_replace("&quot;", "\"", $content);
   
   return preg_match("/\[module=\".+\"\]/", 
   $content);
   
}



function tbname($name){
  require_once "cms-config.php";
  $config = new config();
  return $config->mysql_prefix.$name;
}

//returns version number of UliCMS Core
function cms_version(){
  require_once "version.php";
  return $version;
}


require_once "users_api.php";
require_once "legacy.php";


?>
