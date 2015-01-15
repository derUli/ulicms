<?php

// Magic Quotes Workaround
// Siehe http://php.net/manual/de/security.magicquotes.php /*
if(function_exists("get_magic_quotes_gpc"))
    {
     if (get_magic_quotes_gpc())
         unfck_gpc();
    
     }


 function unfck($v){
     return is_array($v) ? array_map('unfck', $v) : stripslashes($v);
     }

 function unfck_gpc(){
     foreach (array('POST', 'GET', 'REQUEST', 'COOKIE') as $gpc)
     $GLOBALS["_$gpc"] = array_map('unfck', $GLOBALS["_$gpc"]);
     }

 function unregister_globals(){
     // �berpr�fung, ob Register Globals l�uft
    if(ini_get("register_globals") == "1"){
         // Erstellen einer Liste der Superglobals
        $superglobals = array("_GET", "_POST", "_REQUEST", "_ENV", "_FILES", "_SESSION", "_COOKIES", "_SERVER");
         foreach($GLOBALS as $key => $value){
             // �berpr�fung, ob die Variablen/Arrays zu den Superglobals geh�ren, andernfalls l�schen
            if(!in_array($key, $superglobals) && $key != "GLOBALS"){
                 unset($GLOBALS[$key]);
                 }
             }
         return true;
         }
    else{
         // L�uft Register Globals nicht, gibt es nichts zu tun.
        return true;
         }
     }
unregister_globals();


// Zur Sicherheit alle Get und Request Parameter zum String casten, damit man keine Arrays in der URL eingeben kann.
foreach($_GET as $key => $value){
     @$_GET[$key] = strval($value);
     }

foreach($_REQUEST as $key => $value){
     @$_REQUEST[$key] = strval($value);
     }
