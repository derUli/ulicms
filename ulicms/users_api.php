<?php
// this class contains functions for managing user accounts
function getUsers(){
     $query = db_query("SELECT * FROM " . tbname("admins") . " ORDER by username");
     $users = Array();
     while($row = mysql_fetch_object($query)){
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
         mysql_real_escape_string($name) . "'");
     if(mysql_num_rows($query) > 0){
         return mysql_fetch_assoc($query);
         }else{
         return false;
         }
     }

function getUserById($id){
     $query = db_query("SELECT * FROM " . tbname("admins") . " WHERE id = " . intval($id));
     if(mysql_num_rows($query) > 0){
         return mysql_fetch_assoc($query);
         }else{
         return false;
         }
     }



function adduser($username, $lastname, $firstname, $email, $password, $group){
     $username = mysql_real_escape_string($username);
     $lastname = mysql_real_escape_string($lastname);
     $firstname = mysql_real_escape_string($firstname);
     $email = mysql_real_escape_string($email);
     $password = $password;
     $group = intval($group);
    
     db_query("INSERT INTO " . tbname("admins") . " 
(username,lastname, firstname, email, password, `group`) VALUES('$username',' $lastname','$firstname','$email','" . hash_password($password) . "',$group)");
     $message = "Hallo $firstname,\n\n" .
     "Ein Administrator hat auf http://" . $_SERVER["SERVER_NAME"] . " für dich ein neues Benutzerkonto angelegt.\n\n" .
     "Die Zugangsdaten lauten:\n\n" .
     "Benutzername: $username\n" .
     "Passwort: $password\n";
     $header = "From: " . getconfig("email") . "\n" .
     "Content-type: text/plain; charset=utf-8";
    
     @mail($email, "Dein Benutzer-Account bei " . $_SERVER["SERVER_NAME"], $message, $header);
     }

function user_exists($name){
     $query = db_query("SELECT * FROM " . tbname("admins") .
         " WHERE username = '" . mysql_real_escape_string($name) . "'");
     return mysql_num_rows($query) > 0;
     }

function register_session($user, $redirect = true){
    
     $_SESSION["ulicms_login"] = $user["username"];
     $_SESSION["lastname"] = $user["lastname"];
     $_SESSION["firstname"] = $user["firstname"];
     $_SESSION["email"] = $user["email"];
     $_SESSION["login_id"] = $user["id"];
     $_SESSION["group"] = $user["group"];
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
     $user = mysql_real_escape_string($user);
     $user = getUserByName($user);
    
     if($user){
         if($user["old_encryption"])
             $password = md5($password);
         else
             $password = hash_password($password);
        
         if($user["password"] == $password and
             $user["group"] > 0){
             return $user;
             }
         }
     return false;
     }

?>