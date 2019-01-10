<?php
$permissionChecker = new ACL ();
if (! $permissionChecker->hasPermission ( "install_packages" )) {
	noPerms ();
} else {
	?>
<p>
	<a
		href="<?php echo ModuleHelper::buildMethodCallUrl(PackageController::class, "redirectToPackageView");?>"
		class="btn btn-default btn-back"><i class="fa fa-arrow-left" aria-hidden="true"></i>
 <?php translate("back")?></a>
</p>
<h1><?php translate ( "install_package" );?></h1>
<p>
	<a href="?action=upload_package" class="btn btn-default">
<i class="fa fa-upload"></i> <?php translate("upload_file");?>
	</a>
</p>
<p>
	<a href="?action=available_modules" class="btn btn-default"><i class="fas fa-box"></i> <?php translate ( "from_the_package_source" );?>
	</a>
</p>
<p>
	<a href="http://extend.ulicms.de" class="btn btn-default"
		target="_blank"><i class="fas fa-store-alt"></i> UliCMS eXtend</a>
</p>

<?php
}
