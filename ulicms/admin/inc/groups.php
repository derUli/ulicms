<?php
$acl = new ACL();
if (! $acl->hasPermission("groups")) {
    noperms();
} else {
    include_once "../lib/string_functions.php";
    
    $modified = false;
    $created = false;
    $removed = false;
    
    if (isset($_POST["add_group"])) {
        $acl = new ACL();
        $all_permissions = $acl->getDefaultACL(false, true);
        if (isset($_POST["user_permissons"]) and count($_POST["user_permissons"]) > 0) {
            foreach ($_POST["user_permissons"] as $permission_name) {
                $all_permissions[$permission_name] = true;
            }
        }
        
        $name = trim($_POST["name"]);
        if (! empty($name)) {
            $id = $acl->createGroup($name, $all_permissions);
            $group = new Group($id);
            $languages = array();
            if (isset($_POST["restrict_edit_access_language"]) and count($_POST["restrict_edit_access_language"]) > 0) {
                foreach ($_POST["restrict_edit_access_language"] as $lang) {
                    $languages[] = new Language($lang);
                }
            }
            $group->setLanguages($languages);
            $allowed_tags = StringHelper::isNotNullOrWhitespace($_POST["allowable_tags"]) ? strval($_POST["allowable_tags"]) : null;
            $group->setAllowableTags($allowed_tags);
            $group->save();
            $created = true;
            $name = real_htmlspecialchars($name);
        }
    } else if (isset($_GET["delete"]) and get_request_method() == "POST") {
        $id = intval($_GET["delete"]);
        $acl = new ACL();
        $acl->deleteGroup($id);
        $removed = true;
        if (isset($GLOBALS["permissions"])) {
            unset($GLOBALS["permissions"]);
        }
    } else if (isset($_POST["edit_group"])) {
        $acl = new ACL();
        $all_permissions = $acl->getDefaultACL(false, true);
        
        $id = $_POST["id"];
        
        $group = new Group();
        $group->loadById($id);
        $allowed_tags = StringHelper::isNotNullOrWhitespace($_POST["allowable_tags"]) ? strval($_POST["allowable_tags"]) : null;
        $group->setAllowableTags($allowed_tags);
        $languages = array();
        if (isset($_POST["restrict_edit_access_language"]) and count($_POST["restrict_edit_access_language"]) > 0) {
            foreach ($_POST["restrict_edit_access_language"] as $lang) {
                $languages[] = new Language($lang);
            }
        }
        $group->setLanguages($languages);
        $group->save();
        
        if (isset($_POST["user_permissons"]) and count($_POST["user_permissons"]) > 0) {
            foreach ($_POST["user_permissons"] as $permission_name) {
                $all_permissions[$permission_name] = true;
            }
        }
        
        $name = trim($_POST["name"]);
        $all_permissions = json_encode($all_permissions);
        if (! empty($name)) {
            $acl->updateGroup($id, $name, $all_permissions);
            $modified = true;
            $name = real_htmlspecialchars($name);
        }
        if (isset($GLOBALS["permissions"])) {
            unset($GLOBALS["permissions"]);
        }
    }
    ?>
<h1>
<?php translation("groups");?>
</h1>
<?php
    if ($created) {
        // @FIXME: Das hier lokalisieren
        ?>
<p style='color: green;'>
	Die Gruppe "
	<?php
        
        echo $name;
        ?>
	" wurde erfolgreich angelegt.
</p>
<?php
    }
    ?>
<?php
    if ($modified) {
        ?>
<p style='color: green;'>
<?php translate("changes_was_saved");?>
</p>
<?php
    }
    ?>
<?php
    if ($removed) {
        ?>
<p style='color: green;'><?php translate("group_was_deleted")?></p>
<?php
    }
    ?>
<?php
    if (! isset($_GET["add"]) and ! isset($_GET["edit"])) {
        include "inc/group_list.php";
    } else if (isset($_GET["add"])) {
        if ($acl->hasPermission("groups_create")) {
            include "inc/group_add.php";
        } else {
            noperms();
        }
    } else if (isset($_GET["edit"])) {
        if ($acl->hasPermission("groups_edit")) {
            include "inc/group_edit.php";
        } else {
            noperms();
        }
    }
    ?>
<?php
}
?>