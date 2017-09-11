<?php
$acl = new ACL ();
if ($acl->hasPermission ( "install_packages" )) {
	if (isset ( $_REQUEST ["file"] )) {
		$file = Template::getEscape ( $_REQUEST ["file"] );
		?>
<h1><?php translate("install_package");?></h1>
<p><?php translate("PACKAGE_SUCCESSFULL_UPLOADED", array("%file%" => $file));?></p>
<p>
	[<a href="<?php echo ModuleHelper::buildActionURL("upload_package");?>"><?php translate("install_another_package")?></a>]
</p>
<?php
	}
} else {
	noperms ();
}
