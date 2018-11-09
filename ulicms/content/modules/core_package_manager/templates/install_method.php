<?php
$permissionChecker = new ACL();
if (! $permissionChecker->hasPermission("install_packages")) {
    noPerms();
} else {
    ?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("modules");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h1>
<?php translate ( "install_package" );?>
</h1>
<p>
	<a href="?action=upload_package" class="btn btn-default"><?php translate("upload_file");?>
	</a>
</p>
<p>
	<a href="?action=available_modules" class="btn btn-default"><?php translate ( "from_the_package_source" );?>
	</a>
</p>
<p>
	<a href="http://extend.ulicms.de" class="btn btn-default"
		target="_blank">UliCMS eXtend</a>
</p>
</p>

<?php
}
