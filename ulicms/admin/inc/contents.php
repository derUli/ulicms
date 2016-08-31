<?php
if (! defined ( "ULICMS_ROOT" )) {
	die ( "schlechter Hacker" );
}

$acl = new ACL ();

if ($acl->hasPermission ( "pages" ) or $acl->hasPermission ( "banners" ) or $acl->hasPermission ( "categories" ) or $acl->hasPermission ( "export" ) or $acl->hasPermission ( "forms" )) {
	?>

<h2>
<?php translate("contents");?>
</h2>
<p>
	<strong><?php translate("select_content_type");?> </strong><br /> <br />
	<?php
	
	if ($acl->hasPermission ( "pages" )) {
		?>
	<a href="index.php?action=pages"><?php translate("pages");?></a><br />
	<br />
	<?php
	}
	?>

	<?php
	
	if ($acl->hasPermission ( "forms" )) {
		?><a href='?action=forms'><?php
		
		translate ( "forms" );
		?></a> <br />
				<?php
	}
	?>
		
	<?php
	
	if ($acl->hasPermission ( "banners" )) {
		?>
	<a href="index.php?action=banner"><?php get_translation("advertisements");?></a><br />

	<?php
	}
	?>


	<?php
	
	if ($acl->hasPermission ( "categories" )) {
		?>

	<a href="index.php?action=categories"><?php translate("categories");?></a><br />
	<br />
	<?php
	}
	?>

	<?php
	if ($acl->hasPermission ( "export" ) or $acl->hasPermission ( "import" )) {
		?>
 

<p>
	<strong><?php
		translate ( "import_export" );
		?></strong><br /> <br />
	<?php
	}
	if ($acl->hasPermission ( "export" )) {
		?>

	<a href="index.php?action=export"><?php translate("export");?></a><br />
	<?php
	}
	?>
</p>
<?php
	add_hook ( "content_type_list_entry" );
	?>
	<?php
} else {
	noperms ();
}
?>
