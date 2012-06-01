<?php

// get a config variable
function getconfig($key){
	$connection=MYSQL_CONNECTION;
	$ikey=mysql_real_escape_string($ikey);
	$query=mysql_query("SELECT * FROM ".tbname("settings")." WHERE name='$key'",$connection);
	if(mysql_num_rows($query)>0){
		while($row=mysql_fetch_object($query)){
		return $row->value;
		}
	}
	else{
		return false;
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
		
		if(is_file($module_mainfile_path)){
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
	$query = mysql_query("SELECT * FROM `".tbname("content")."`");
	$returnvalues = Array();
	while($row = mysql_fetch_object($query)){
		array_push($returnvalues, $row->systemname);
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
    mysql_query("INSERT INTO ".tbname("setings"). " (name, value) VALUES('$key', '$value')");
  }

}





function tbname($name){
  require_once "cms-config.php";
  $config=new config();
  return $config->mysql_prefix.$name;
}

//returns version number of UliCMS Core
function cms_version(){
  require_once "version.php";
  return $version;
}



require_once "legacy.php";


?>
