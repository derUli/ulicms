<?php
/* Diese Datei initalisiert die Datenbankverbindung
   Bitte ab Release 3.0.1 zusätzliche Funktionen nicht mehr direkt in die init.php
   eintragen sondern in die functions.php
*/
require_once "workaround.php";
require_once "cms-config.php";
require_once "api.php";



session_start();
$_COOKIE[session_name()] = session_id();












function noperms(){
	echo "<p>Sie haben nicht die Berechtigung, um auf diese Seite zugreifen zu dürfen.<br/>Bitte loggen Sie sich als <u>admin</u> ein oder fragen Sie den Administrator der Webseite.</p>";
	return false;
}

function startsWith($haystack,$needle,$case=true) {
    if($case){return (strcmp(substr($haystack, 0, strlen($needle)),$needle)===0);}
    return (strcasecmp(substr($haystack, 0, strlen($needle)),$needle)===0);
}

function endsWith($haystack,$needle,$case=true) {
    if($case){return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);}
    return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);
}


global $connection,$config;
$config=new config();
@$connection=mysql_connect($config->mysql_server,$config->mysql_user, $config->mysql_password);
if($connection==false){
die("Fehler: Die Verbindung zum MySQL Server konnte nicht hergestellt werden.");
}


define("MYSQL_CONNECTION",$connection);
$select=mysql_select_db($config->mysql_database);
if(!$select){
die("Fehler: Die Datenbank ".$config->mysql_database." existiert nicht.");
}


@include "functions.php";

?>
