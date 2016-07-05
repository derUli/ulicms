<?php
if ($acl->hasPermission ( "update_system" )) {
	$updateInfo = checkForUpdates ();
	
	if ($updateInfo) {
		echo $updateInfo;
	}
}