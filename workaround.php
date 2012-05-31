<?php
error_reporting(E_ALL ^ E_NOTICE);
if (get_magic_quotes_gpc()) unfck_gpc();

 function unfck($v) {
   return is_array($v) ? array_map('unfck', $v) : stripslashes($v);
 }

 function unfck_gpc() {
   foreach (array('POST', 'GET', 'REQUEST', 'COOKIE') as $gpc)
   $GLOBALS["_$gpc"] = array_map('unfck', $GLOBALS["_$gpc"]);
 }
 
 function unregister_globals() {
    // Überprüfung, ob Register Globals läuft
    if(ini_get("register_globals") == "1") {
        // Erstellen einer Liste der Superglobals
        $superglobals=array("_GET", "_POST", "_REQUEST", "_ENV", "_FILES", "_SESSION", "_COOKIES", "_SERVER");
        foreach($GLOBALS as $key => $value) {
            // Überprüfung, ob die Variablen/Arrays zu den Superglobals gehören, andernfalls löschen
            if(!in_array($key, $superglobals) && $key != "GLOBALS") {
                unset($GLOBALS[$key]);
            }
        }
        return true;
    }
    else {
        // Läuft Register Globals nicht, gibt es nichts zu tun.
        return true;
    }
}
unregister_globals();

 
?>