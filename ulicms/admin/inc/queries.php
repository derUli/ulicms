<?php
$acl = new ACL ();
add_hook ( "query" );

include_once ULICMS_ROOT . "/classes/objects/content/vcs.php";

if ($acl->hasPermission ( "modules" ) and Request::getVar ( "toggle-show-core-modules" )) {
	$_SESSION ["show_core_modules"] = ! $_SESSION ["show_core_modules"];
	Request::redirect ( ModuleHelper::buildActionURL ( Request::getVar ( "action" ) ) );
}
