<?php
$acl = new ACL ();
?>
<h1><?php translate("settings");?></h1>
<?php

if ($acl->hasPermission ( "settings_simple" ) or $acl->hasPermission ( "design" ) or $acl->hasPermission ( "spam_filter" ) or $acl->hasPermission ( "cache" ) or $acl->hasPermission ( "motd" ) or $acl->hasPermission ( "pkg_settings" ) or $acl->hasPermission ( "logo" ) or $acl->hasPermission ( "languages" ) or $acl->hasPermission ( "other" )) {
	?>
<p>
<?php
	
	if ($acl->hasPermission ( "settings_simple" )) {
		?>
	<a href="index.php?action=settings_simple"><?php translate("general_settings");?>
	</a> <br /> <br />
	<?php
	}
	?>
<?php
	if ($acl->hasPermission ( "design" )) {
		?>
	<a href="index.php?action=design"><?php translate("design");?>
	</a> <br /> <br />
	<?php
	}
	?>
<?php
	
	if ($acl->hasPermission ( "spam_filter" )) {
		?>
	<a href="index.php?action=spam_filter"><?php translate("spamfilter");?>
	</a> <br /> <br />
	<?php
	}
	?>
	<?php
	
	if ($acl->hasPermission ( "privacy_settings" )) {
		?>
	<a href="?action=privacy_settings"><?php translate("privacy");?>
	</a><br /> <br />
	<?php
	}
	?>
<?php
	
	if ($acl->hasPermission ( "cache" )) {
		?>
	<a
		href="<?php echo ModuleHelper::buildMethodCallUrl("CacheSettingsController", "clearCache");?>"><?php
		translate ( "clear_cache" );
		?>
	</a> <br /> <br />
	<?php
	}
	?>
<?php
	
	if ($acl->hasPermission ( "motd" )) {
		?>
	<a href="index.php?action=motd"><?php translate("motd");?>
	</a> <br /> <br />
	<?php
	}
	?>
<?php
	
	if ($acl->hasPermission ( "pkg_settings" )) {
		?>
	<a href="?action=pkg_settings"><?php
		translate ( "package_source" );
		?>
	</a> <br /> <br />
	<?php
	}
	?>
<?php
	
	if ($acl->hasPermission ( "logo" )) {
		?>
	<a href="index.php?action=logo_upload"><?php translate("logo");?>
	</a> <br /> <br />
	<?php
	}
	?>
<?php
	
	if ($acl->hasPermission ( "languages" )) {
		?>
	<a href="index.php?action=languages"><?php translate("languages");?>
	</a> <br /> <br />
	<?php
	}
	?>
	<?php if ($acl->hasPermission ( "other" )) {
		?>
	<a href="?action=other_settings"><?php translate("other");?>
	</a>
 	<?php
 	}
 	?>
</p>
<?php
} else {
	noPerms ();
}

?>
