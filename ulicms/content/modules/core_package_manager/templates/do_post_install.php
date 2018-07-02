<?php
$acl = new ACL ();
if ($acl->hasPermission ( "update_system" )) {
	?>
<h1><?php translate("run_post_install_script");?></h1>
<?php
	$postinstall = ULICMS_DATA_STORAGE_ROOT . "/post-install.php";
	if (is_file ( $postinstall )) {
		include $postinstall;
		unlink ( $postinstall );
		?>
		<?php if(!is_file($postinstall)){?>
<p><?php translate("finished");?></p>
<?php } ?>
<?php
	} else {
		?>
<p><?php translate("file_not_found");?></p>
<?php
	}
} else {
	noperms ();
}
