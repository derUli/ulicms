<?php
include_once dirname(__file__) . "/lib/encryption.php";

// this class contains functions for managing user accounts
function getUsers(){
     $query = db_query("SELECT * FROM " . tbname("admins") . " ORDER by username");
     $users = Array();
     while($row = db_fetch_object($query)){
         array_push($users, $row -> username);
         }
    
     return $users;
     }

function changePassword($password, $id){
     include_once "../lib/encryption.php";
     $newPassword = hash_password($password);
     return db_query("UPDATE " . tbname("admins") . " SET `password` = '$newPassword',  `old_encryption` = 0 WHERE id = $id");
     }

function getUserByName($name){
     $query = db_query("SELECT * FROM " . tbname("admins") . " WHERE username='" .
         db_escape($name) . "'");
     if(db_num_rows($query) > 0){
         return db_fetch_assoc($query);
         }else{
         return false;
         }
     }

function getUserById($id){
     $query = db_query("SELECT * FROM " . tbname("admins") . " WHERE id = " . intval($id));
     if(db_num_rows($query) > 0){
         return db_fetch_assoc($query);
         }else{
         return false;
         }
     }



function adduser($username, $lastname, $firstname, $email, $password, $group, $sendMessage = true, $acl_group = null){
     $username = db_escape($username);
     $lastname = db_escape($lastname);
     $firstname = db_escape($firstname);
     $email = db_escape($email);
     $password = $password;
     // legacy group
    $group = intval($group);
     // Default ACL Group
    if(!$acl_group)
         $acl_group = getconfig("default_acl_group");
    
     if(is_null($acl_group))
         $acl_group = "NULL";
    
     db_query("INSERT INTO " . tbname("admins") . " 
(username,lastname, firstname, email, password, `group`, `group_id`) VALUES('$username',' $lastname','$firstname','$email','" . hash_password($password) . "',$group, $acl_group)");
     $message = "Hallo $firstname,\n\n" .
     "Ein Administrator hat auf http://" . $_SERVER["SERVER_NAME"] . " für dich ein neues Benutzerkonto angelegt.\n\n" .
     "Die Zugangsdaten lauten:\n\n" .
     "Benutzername: $username\n" .
     "Passwort: $password\n";
     $header = "From: " . getconfig("email") . "\n" .
     "Content-type: text/plain; charset=utf-8";
    
     if($sendMessage){
         @mail($email, "Dein Benutzer-Account bei " . $_SERVER["SERVER_NAME"], $message, $header);
         }
     }

function user_exists($name){
     $query = db_query("SELECT * FROM " . tbname("admins") .
         " WHERE username = '" . db_escape($name) . "'");
     return db_num_rows($query) > 0;
     }

function register_session($user, $redirect = true){
    
     $_SESSION["ulicms_login"] = $user["username"];
     $_SESSION["lastname"] = $user["lastname"];
     $_SESSION["firstname"] = $user["firstname"];
     $_SESSION["email"] = $user["email"];
     $_SESSION["login_id"] = $user["id"];
     // Soll durch group_id und eine ACL ersetzt werden
    $_SESSION["group"] = $user["group"];
    
     // Group ID
    $_SESSION["group_id"] = $user["group_id"];
    
     $_SESSION["logged_in"] = true;
     if(is_null($_SESSION["group_id"]))
         $_SESSION["group_id"] = 0;
    
     $_SESSION["session_begin"] = time();
    
     if(!$redirect)
         return;
    
     if(isset($_REQUEST["go"]))
         header("Location: " . $_REQUEST["go"]);
     else
         header("Location: index.php");
    
     return;
    
    
     }


function validate_login($user, $password){
     include_once "../lib/encryption.php";
     $user = db_escape($user);
     $user = getUserByName($user);
    
     if($user){
         if($user["old_encryption"])
             $password = md5($password);
         else
             $password = hash_password($password);
        
         if($user["password"] == $password){
             return $user;
             }
         }
     return false;
     }

?>