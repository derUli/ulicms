<?php
$acl = new ACL ();
if ($acl->hasPermission ( "polls_edit" ) and isset ( $_REQUEST ["poll_stats"] )) {
	echo Template::executeModuleTemplate ( "polls", "admin/stats" );
	exit ();
}
