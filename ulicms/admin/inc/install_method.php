<?php
$acl = new ACL ();
if (! $acl->hasPermission ( "install_packages" )) {
	noperms ();
} else {
	?>
<h1>
<?php translate ( "install_package" );?>
</h1>
<p>
	<a href="?action=upload_package"><?php translate("upload_file");?>
	</a> <br /> <a href="?action=available_modules"><?php translate ( "from_the_package_source" );?>
	</a> <br /> 
	<a href="http://extend.ulicms.de" target="_blank">UliCMS eXtend</a>
	</a>
</p>

<?php
}

?>