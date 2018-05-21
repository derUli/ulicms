<?php
function checkIfSystemnameIsFree($systemname, $language, $id) {
	if (StringHelper::isNullOrWhitespace ( $systemanme )) {
		return true;
	}
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
		
		esc($page ["title"]);
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
	case "getContentTypes" :
		JSONResult ( DefaultContentTypes::getAll () );
		break;
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
	case "available_modules" :
		include_once "inc/ajax_available_modules.php";
		break;
	case "getPageListByLang" :
		ajaxOnChangeLanguage ( $_REQUEST ["mlang"], $_REQUEST ["mmenu"], $_REQUEST ["mparent"] );
		break;
	default :
		echo "Unknown Call";
		break;
}
?>
