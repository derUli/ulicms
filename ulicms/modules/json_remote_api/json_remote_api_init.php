<?php 


if(isset($_REQUEST["remote_user"]) and isset($_REQUEST["remote_password"])){
     $sessionData = validate_login($_REQUEST["remote_user"], $_REQUEST["remote_password"]);
     if($sessionData){
         define("REMOTE_API_AUTHENTIFICATION_OK", "OK");
     }
}


if($_REQUEST["remote_user"]){
   header("Content-Type: application/json; charset=utf-8");
}

if(defined("REMOTE_API_AUTHENTIFICATION_OK") and isset($_REQUEST["remote_user"])){
  // Weiter funktionen händeln
    add_hook("remote_api");
} else if(!defined("REMOTE_API_AUTHENTIFICATION_OK") and isset($_REQUEST["remote_user"])){
  $result = array("error" => "login_invalid");
  die(json_encode($result));
}
