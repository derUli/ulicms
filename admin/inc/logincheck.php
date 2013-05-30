<?php
if(isset($_GET["destroy"]) or $_GET["action"]=="destroy"){
	
	mysql_query("UPDATE ".tbname("admins")." SET last_action = 0 WHERE id = ".$_SESSION["login_id"]);
	session_destroy();
	header("Location: index.php");
	exit();
}

if(isset($_POST["login"])){
        include_once "../lib/encryption.php";
	$user = mysql_real_escape_string($_POST["user"]);
	$user = getUserByName($user);
	
	var_dump($user);
	if($user){
	   if($user["old_encryption"])
              $password = md5($_POST["password"]);
           else
	      $password = hash_password($_POST["password"]);
	      	   
	   if($user["password"] == $password and
	      $user["group"] > 0){
              $data=mysql_fetch_array($query);
              $_SESSION["ulicms_login"] = $user["username"];
              $_SESSION["lastname"] = $user["lastname"];
              $_SESSION["firstname"] = $user["firstname"];     
              $_SESSION["email"] = $user["email"];
              $_SESSION["login_id"] = $user["id"];
              $_SESSION["group"] = $user["group"];
              $_SESSION["session_begin"] = time();
              
              if(isset($_REQUEST["go"]))
                  header("Location: ".$_REQUEST["go"]);
              else
                  header("Location: index.php");
		exit();
          }
      
     }
	

}

?>