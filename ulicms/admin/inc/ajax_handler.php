<?php
function sendChatMessage() {
	if (! empty ( $_REQUEST ["to"] ) and ! empty ( $_REQUEST ["message"] ) and isset ( $_SESSION ["ulicms_login"] )) {
		$to = db_escape ( $_REQUEST ["to"] );
		$message = db_escape ( $_REQUEST ["message"] );
		$from = db_escape ( $_SESSION ["ulicms_login"] );
		$time = time ();
		
		db_query ( "INSERT INTO " . tbname ( "chat_messages" ) . " (`from`, `to`, message, date, `read`) VALUES('$from', '$to', '$message', $time, 0)" );
	} else {
		return;
	}
}

$ajax_cmd = $_REQUEST ["ajax_cmd"];

switch ($ajax_cmd) {
	case "users_online" :
		include "inc/users_online.php";
		break;
	case "available_modules" :
		include_once "inc/ajax_available_modules.php";
		break;
	case "users_online_dashboard" :
		include "inc/users_online_dashboard.php";
		break;
	case "sendChatMessage" :
		sendChatMessage ();
		break;
	default :
		echo "Unknown Call";
		break;
}
?>