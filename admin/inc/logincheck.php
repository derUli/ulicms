<?php
if(isset($_GET["destroy"]) or $_GET["action"]=="destroy"){
	session_destroy();
	header("Location: index.php");
	exit();
}

if(isset($_POST["login"])){
	$user=mysql_real_escape_string($_POST["user"]);
	$password=md5($_POST["password"]);
	$query=mysql_query("SELECT * FROM ".tbname("admins")." WHERE username='$user' AND password='$password'",$connection);
	if(mysql_num_rows($query)>0){
		$data=mysql_fetch_array($query);
		$_SESSION["ulicms_login"]=$data["username"];
		$_SESSION["lastname"]=$data["lastname"];
		$_SESSION["firstname"]=$data["firstname"];     
		$_SESSION["email"]=$data["email"];
		$_SESSION["login_id"]=$data["id"];
		$_SESSION["group"]=$data["group"];

		header("Location: index.php");
		exit();
	}

}

?>