<?php
$acl = new ACL ();
if (is_admin () or $acl->hasPermission ( "update_system" )) {
	if (file_exists ( Path::resolve ( "ULICMS_ROOT/update.php" ) )) {
		?>
<p>
	<a href="../update.php"><?php translate("run_update");?></a>
</p>
<p><?php translate("update_notice");?>
	<?php
	} else {
		translate ( "update_information_text" );
		?>
</p>
<?php
	}
} else {
	noperms ();
}
