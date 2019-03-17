<?php

function checkIfSystemnameIsFree($systemname, $language, $id)
{
    if (StringHelper::isNullOrWhitespace($systemname)) {
        return true;
    }
    $systemname = Database::escapeValue($systemname);
    $language = Database::escapeValue($language);
    $id = intval($id);
    $sql = "SELECT id FROM " . tbname("content") . " where systemname='$systemname' and language = '$language' ";
    if ($id > 0) {
        $sql .= "and id <> $id";
    }
    $result = Database::query($sql);
    return (Database::getNumRows($result) <= 0);
}

function ajaxOnChangeLanguage($lang, $menu, $parent)
{
    ?>
<option selected="selected" value="NULL">
			[ 
			<?php
    
    translate("none");
    ?>
			]
		</option>
<?php
    $pages = getAllPages($lang, "title", false, $menu);
    foreach ($pages as $key => $page) {
        ?>
<option value="<?php
        
        echo $page["id"];
        ?>"
	<?php if($page["id"] == $parent) echo "selected";?>>
				<?php
        
        echo esc($page["title"]);
        ?>
				(ID:
				<?php
        
        echo $page["id"];
        ?>
				)
			</option>
<?php
    }
}

$ajax_cmd = $_REQUEST["ajax_cmd"];

switch ($ajax_cmd) {
    case "toggle_show_positions":
        $permissionChecker = new ACL();
        if (! $permissionChecker->hasPermission("pages_show_positions")) {
            TextResult("Access Denied", HttpStatusCode::FORBIDDEN);
        }
        $settingsName = "user/" . get_user_id() . "/show_positions";
        if (Settings::get($settingsName)) {
            Settings::delete($settingsName);
        } else {
            Settings::set($settingsName, "1");
        }
        break;
    case "toggle_show_filters":
        $permissionChecker = new ACL();
        if (! $permissionChecker->hasPermission("pages")) {
            TextResult("Access Denied", HttpStatusCode::FORBIDDEN);
        }
        $settingsName = "user/" . get_user_id() . "/show_filters";
        if (Settings::get($settingsName)) {
            Settings::delete($settingsName);
        } else {
            Settings::set($settingsName, "1");
        }
        break;
    case "check_if_systemname_is_free":
        if (checkIfSystemnameIsFree($_REQUEST["systemname"], $_REQUEST["language"], intval($_REQUEST["id"]))) {
            echo "yes";
        }
        break;
    case "available_modules":
        require_once "inc/ajax_available_modules.php";
        break;
    case "getPageListByLang":
        ajaxOnChangeLanguage($_REQUEST["mlang"], $_REQUEST["mmenu"], $_REQUEST["mparent"]);
        break;
    default:
        echo "Unknown Call";
        break;
}
