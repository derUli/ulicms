<?php

// Bei jedem Aufruf muss remote_user und remote_password
// gesetzt sein (GET/POST Parameter)
if(isset($_REQUEST["remote_user"]) and isset($_REQUEST["remote_password"])){
     $sessionData = validate_login($_REQUEST["remote_user"], $_REQUEST["remote_password"]);
     if($sessionData){
         define("REMOTE_API_AUTHENTIFICATION_OK", "OK");
    }
}


// Content-Type zu JSON setzen
// JSON ist die Notation wie Objekte in JavaScript dargestellt werden
if(isset($_REQUEST["remote_user"])){
     header("Content-Type: application/json; charset=utf-8");
}


// Wenn die Zugangsdaten korrekt sind
if(defined("REMOTE_API_AUTHENTIFICATION_OK") and isset($_REQUEST["remote_user"])){
     // remote_api Hook aufrufen
     // Ein Beispiel für die remote_api Hook findet sich in der Datei json_remote_api_remote_api.php
    add_hook("remote_api");

    
     // Wenn kein Remote Call aufgerufen wurde.
     // Dieser fehler kommt auch, falls bei keinem Modul eine
     // remote_api Hook vorhanden ist
     
     $result = array("error" => "no_such_call");
     die(json_encode($result));
    
    } else if(!defined("REMOTE_API_AUTHENTIFICATION_OK") and isset($_REQUEST["remote_user"])){
     // Ansonsten Fehlermeldung
    $result = array("error" => "login_invalid");
     die(json_encode($result));
    }
