<?php
$acl = new ACL ();
if ($acl->hasPermission ( "update_system" ) and !Settings::get("disable_core_patch_check")) {
	$data = file_get_contents_wrapper ( PATCH_CHECK_URL, true );
	$data = trim ( $data );
	if (! empty ( $data )) {
		?>

<a href="?action=available_patches"><strong><?php translate ( "install_patches" );?></strong>
</a>
<?php
	}
}
