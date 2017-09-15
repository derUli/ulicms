<?php
$acl = new ACL ();
add_hook ( "query" );

include_once ULICMS_ROOT . "/classes/objects/content/vcs.php";

if ($acl->hasPermission ( "modules" ) and Request::getVar ( "toggle-show-core-modules" )) {
	$_SESSION ["show_core_modules"] = ! $_SESSION ["show_core_modules"];
	Request::redirect ( ModuleHelper::buildActionURL ( Request::getVar ( "action" ) ) );
}

if ($_GET ["action"] == "banner_delete" && $acl->hasPermission ( "banners" ) && get_request_method () == "POST") {
	$banner = intval ( $_GET ["banner"] );
	add_hook ( "before_banner_delete" );
	$query = db_query ( "DELETE FROM " . tbname ( "banner" ) . " WHERE id='$banner'", $connection );
	add_hook ( "after_banner_delete" );
	header ( "Location: index.php?action=banner" );
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
