<?php
$acl = new ACL ();
if ($acl->hasPermission ( "emergency_password_reset" )) {
	?>
<?php translate("all_password_were_resetted");?>
<?php

} else {
	noperms ();
}
?>