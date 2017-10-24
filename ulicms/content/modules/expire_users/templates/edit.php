<?php
$acl = new ACL ();
$permission = ( array ) getModuleMeta ( "expire_users", "action_permissions" );
$permission = $permission ["edit_expire_user"];
if ($acl->hasPermission ( $permission )) {
	?>
Edit is not implemented yet.
<?php }?>
