<?php
if(isset($_GET["destroy"]) or $_GET["action"]=="destroy"){
	db_query("UPDATE ".tbname("admins")." SET last_action = 0 WHERE id = ".$_SESSION["login_id"]);
	header("Location: index.php");
	
	session_destroy();
	exit();
}

if(isset($_POST["login"])){
   $sessionData = validate_login($_POST["user"], $_POST["password"]);
   if($sessionData){
      add_hook("login_ok");
      register_session($sessionData, true);
   }
   else{
      add_hook("login_failed");
   }
}

?>
