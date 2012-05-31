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