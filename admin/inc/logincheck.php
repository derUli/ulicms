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
	
	if($user){
	   if($user["old_encryption"])
              $password = md5($_POST["password"]);
           else
	      $password = hash_password($_POST["password"]);
	   
	   
	   if($user["password"] == $password and
	      $user["group"] > 0){
              $data=mysql_fetch_array($query);
              $_SESSION["ulicms_login"]=$data["username"];
              $_SESSION["lastname"]=$data["lastname"];
              $_SESSION["firstname"]=$data["firstname"];     
              $_SESSION["email"]=$data["email"];
              $_SESSION["login_id"]=$data["id"];
              $_SESSION["group"]=$data["group"];
              $_SESSION["session_begin"] = time();
              
              if(isset($_REQUEST["go"]))
                  header("Location: ".$_REQUEST["go"]);
              else
                  header("Location: index.php");
		exit();
          }
      
}
	
	}
	
	$query = mysql_query("SELECT * FROM ".tbname("admins")." WHERE username='$user' AND password='$password' AND `group` > 0");
	if(mysql_num_rows($query)>0){
		

}

?>