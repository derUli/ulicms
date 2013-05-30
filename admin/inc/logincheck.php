<?php
if(isset($_GET["destroy"]) or $_GET["action"]=="destroy"){
	
	mysql_query("UPDATE ".tbname("admins")." SET last_action = 0 WHERE id = ".$_SESSION["login_id"]);
	session_destroy();
	header("Location: index.php");
	exit();
}

if(isset($_POST["login"])){
   $sessionData = validate_login($_POST["user"], $_POST["password"]);
   if($sessionData)
      register_session($sessionData, true);
}

?>