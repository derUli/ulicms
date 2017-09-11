<?php
$acl = new ACL ();
add_hook ( "query" );

include_once ULICMS_ROOT . "/classes/objects/content/vcs.php";

if ($acl->hasPermission ( "pages" ) and Request::getVar ( "toggle-show-core-modules" )) {
	$_SESSION ["show_core_modules"] = ! $_SESSION ["show_core_modules"];
	Request::redirect ( ModuleHelper::buildActionURL ( Request::getVar ( "action" ) ) );
}

if ($_GET ["action"] == "view_website" or $_GET ["action"] == "frontpage") {
	add_hook ( "before_view_website" );
	Request::redirect ( "../" );
}

if ($_GET ["action"] == "pages_delete" && $acl->hasPermission ( "pages" ) && get_request_method () == "POST") {
	$page = intval ( $_GET ["page"] );
	add_hook ( "before_delete_page" );
	db_query ( "UPDATE " . tbname ( "content" ) . " SET `deleted_at` = " . time () . " WHERE id=$page" );
	add_hook ( "after_delete_page" );
	header ( "Location: index.php?action=pages" );
	exit ();
}

if ($_GET ["action"] == "empty_trash") {
	add_hook ( "before_empty_trash" );
	db_query ( "DELETE FROM " . tbname ( "content" ) . " WHERE deleted_at IS NOT NULL" );
	add_hook ( "after_empty_trash" );
	header ( "Location: index.php?action=pages" );
	exit ();
}

if ($_GET ["action"] == "banner_delete" && $acl->hasPermission ( "banners" ) && get_request_method () == "POST") {
	$banner = intval ( $_GET ["banner"] );
	add_hook ( "before_banner_delete" );
	$query = db_query ( "DELETE FROM " . tbname ( "banner" ) . " WHERE id='$banner'", $connection );
	add_hook ( "after_banner_delete" );
	header ( "Location: index.php?action=banner" );
	exit ();
}

if ($_GET ["action"] == "admin_delete" && (is_admin () or $acl->hasPermission ( "users" )) && get_request_method () == "POST") {
	$admin = intval ( $_GET ["admin"] );
	add_hook ( "before_admin_delete" );
	$query = db_query ( "DELETE FROM " . tbname ( "users" ) . " WHERE id='$admin'", $connection );
	add_hook ( "after_admin_delete" );
	header ( "Location: index.php?action=admins" );
	exit ();
}

if ($_POST ["add_banner"] == "add_banner" && $acl->hasPermission ( "banners" )) {
	
	$name = db_escape ( $_POST ["banner_name"] );
	$image_url = db_escape ( $_POST ["image_url"] );
	$link_url = db_escape ( $_POST ["link_url"] );
	$category = intval ( $_POST ["category"] );
	$type = db_escape ( $_POST ["type"] );
	$html = db_escape ( $_POST ["html"] );
	$language = db_escape ( $_POST ["language"] );
	
	add_hook ( "before_create_banner" );
	$query = db_query ( "INSERT INTO " . tbname ( "banner" ) . "
(name,link_url,image_url, category, `type`, html, `language`) VALUES('$name','$link_url','$image_url', '$category', '$type', '$html',
'$language')", $connection );
	
	add_hook ( "after_create_banner" );
	header ( "Location: index.php?action=banner" );
	exit ();
}

if ($_POST ["add_admin"] == "add_admin" && (is_admin () or $acl->hasPermission ( "users" ))) {
	$username = $_POST ["admin_username"];
	$lastname = $_POST ["admin_lastname"];
	$firstname = $_POST ["admin_firstname"];
	$password = $_POST ["admin_password"];
	$email = $_POST ["admin_email"];
	$default_language = StringHelper::isNotNullOrWhitespace ( $_POST ["default_language"] ) ? $_POST ["default_language"] : null;
	$sendMail = isset ( $_POST ["send_mail"] );
	$admin = intval ( isset ( $_POST ["admin"] ) );
	$locked = intval ( isset ( $_POST ["locked"] ) );
	$group_id = intval ( $_POST ["group_id"] );
	if ($group_id <= 0) {
		$group_id = null;
	}
	$require_password_change = intval ( isset ( $_POST ["require_password_change"] ) );
	adduser ( $username, $lastname, $firstname, $email, $password, $sendMail, $group_id, $require_password_change, $admin, $locked, $default_language );
	header ( "Location: index.php?action=admins" );
	exit ();
}

if (($_POST ["edit_admin"] == "edit_admin" && $acl->hasPermission ( "users" )) or ($_POST ["edit_admin"] == "edit_admin" and logged_in () and $_POST ["id"] == $_SESSION ["login_id"])) {
	
	$id = intval ( $_POST ["id"] );
	$username = db_escape ( $_POST ["admin_username"] );
	$lastname = db_escape ( $_POST ["admin_lastname"] );
	$firstname = db_escape ( $_POST ["admin_firstname"] );
	$email = db_escape ( $_POST ["admin_email"] );
	$password = $_POST ["admin_password"];
	// User mit eingeschränkten Rechten darf sich nicht selbst zum Admin machen können
	if ($acl->hasPermission ( "users" )) {
		$admin = intval ( isset ( $_POST ["admin"] ) );
		if (isset ( $_POST ["group_id"] )) {
			$group_id = $_POST ["group_id"];
			if ($group_id == "-") {
				$group_id = "NULL";
			} else {
				$group_id = intval ( $group_id );
			}
		} else {
			$group_id = $_SESSION ["group_id"];
		}
	} else {
		$user = getUserById ( $id );
		$admin = $user ["admin"];
		$group_id = $user ["group_id"];
		if (is_null ( $group_id )) {
			$group_id = "NULL";
		}
	}
	
	$notify_on_login = intval ( isset ( $_POST ["notify_on_login"] ) );
	
	$twitter = db_escape ( $_POST ["twitter"] );
	$homepage = db_escape ( $_POST ["homepage"] );
	$skype_id = db_escape ( $_POST ["skype_id"] );
	$about_me = db_escape ( $_POST ["about_me"] );
	$html_editor = db_escape ( $_POST ["html_editor"] );
	$require_password_change = intval ( isset ( $_POST ["require_password_change"] ) );
	$locked = intval ( isset ( $_POST ["locked"] ) );
	
	$default_language = StringHelper::isNotNullOrWhitespace ( $_POST ["default_language"] ) ? "'" . Database::escapeValue ( $_POST ["default_language"] ) . "'" : "NULL";
	
	add_hook ( "before_edit_user" );
	$sql = "UPDATE " . tbname ( "users" ) . " SET username = '$username', `group_id` = " . $group_id . ", `admin` = $admin, firstname='$firstname',
lastname='$lastname', notify_on_login='$notify_on_login', email='$email', skype_id = '$skype_id',
about_me = '$about_me', html_editor='$html_editor', require_password_change='$require_password_change', `locked`='$locked', `twitter` = '$twitter', `homepage` = '$homepage' , `default_language` = $default_language WHERE id=$id";
	
	db_query ( $sql );
	
	if (! empty ( $password )) {
		changePassword ( $password, $id );
	}
	
	add_hook ( "after_edit_user" );
	;
	if (! $acl->hasPermission ( "users" )) {
		header ( "Location: index.php" );
		exit ();
	} else {
		header ( "Location: index.php?action=admins" );
		exit ();
	}
}

if ($_POST ["edit_banner"] == "edit_banner" && $acl->hasPermission ( "banners" )) {
	$name = db_escape ( $_POST ["banner_name"] );
	$image_url = db_escape ( $_POST ["image_url"] );
	$link_url = db_escape ( $_POST ["link_url"] );
	$category = intval ( $_POST ["category"] );
	$id = intval ( $_POST ["id"] );
	
	$type = db_escape ( $_POST ["type"] );
	$html = db_escape ( $_POST ["html"] );
	$language = db_escape ( $_POST ["language"] );
	add_hook ( "before_edit_banner" );
	$query = db_query ( "UPDATE " . tbname ( "banner" ) . "
SET name='$name', link_url='$link_url', image_url='$image_url', category='$category', type='$type', html='$html', language='$language' WHERE id=$id" );
	
	add_hook ( "after_edit_banner" );
	header ( "Location: index.php?action=banner" );
	exit ();
}
