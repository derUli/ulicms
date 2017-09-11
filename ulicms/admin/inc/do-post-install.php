<?php
$acl = new ACL ();
if ($acl->hasPermission ( "update_system" )) {
	?>
<h1><?php translate("run_post_install_script");?></h1>
<?php
	$postinstall = ULICMS_ROOT . "/post-install.php";
	include $postinstall;
	unlink ( $postinstall );
	?>
		<?php if(!file_exists($postinstall)){?>
<p><?php translate("finished");?></p>
<?php } ?>
<?php
} else {
	noperms ();
}