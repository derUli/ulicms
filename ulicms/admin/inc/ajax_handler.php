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
function checkIfSystemnameIsFree($systemname, $language, $id) {
	$systemname = Database::escapeValue ( $systemname );
	$language = Database::escapeValue ( $language );
	$id = intval ( $id );
	$sql = "SELECT id FROM " . tbname ( "content" ) . " where systemname='$systemname' and language = '$language' ";
	if ($id > 0) {
		$sql .= "and id <> $id";
	}
	$result = Database::query ( $sql );
	return (Database::getNumRows ( $result ) <= 0);
}
function ajaxOnChangeLanguage($lang, $menu, $parent) {
	?>
<option selected="selected" value="NULL">
			[
			<?php
	
	translate ( "none" );
	?>
			]
		</option>
<?php
	$pages = getAllPages ( $lang, "title", false, $menu );
	foreach ( $pages as $key => $page ) {
		?>
<option value="<?php
		
		echo $page ["id"];
		?>"
	<?php if($page["id"] == $parent) echo "selected";?>>
				<?php
		
		echo $page ["title"];
		?>
				(ID:
				<?php
		
		echo $page ["id"];
		?>
				)
			</option>
<?php
	}
}

$ajax_cmd = $_REQUEST ["ajax_cmd"];

switch ($ajax_cmd) {
	case "check_if_systemname_is_free" :
		if (checkIfSystemnameIsFree ( $_REQUEST ["systemname"], $_REQUEST ["language"], intval ( $_REQUEST ["id"] ) )) {
			echo "yes";
		}
		break;
	case "core_update_check" :
		include "inc/ajax_core_update_check.php";
		break;
	case "ajax_patch_check" :
		include "inc/ajax_patch_check.php";
		break;
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
	case "getPageListByLang" :
		ajaxOnChangeLanguage ( $_REQUEST ["mlang"], $_REQUEST ["mmenu"], $_REQUEST ["mparent"] );
		break;
	default :
		echo "Unknown Call";
		break;
}
?>