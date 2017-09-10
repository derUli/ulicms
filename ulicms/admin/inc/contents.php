<?php
$acl = new ACL ();
if ($acl->hasPermission ( "pages" ) or $acl->hasPermission ( "banners" ) or $acl->hasPermission ( "categories" ) or $acl->hasPermission ( "export" ) or $acl->hasPermission ( "forms" )) {
	?>
<h2><?php translate("contents");?></h2>
<p>
	<strong><?php translate("select_content_type");?> </strong><br /> <br />
	<?php
	
	if ($acl->hasPermission ( "pages" )) {
		?>
	<a href="index.php?action=pages"><?php translate("pages");?></a><br />
	<br />
	<?php
	}
	if ($acl->hasPermission ( "forms" )) {
		?><a href='?action=forms'><?php
		
		translate ( "forms" );
		?></a> <br />
				<?php
	}
	if ($acl->hasPermission ( "banners" )) {
		?>
	<a href="index.php?action=banner"><?php translate("advertisements");?></a><br />

	<?php
	}
	if ($acl->hasPermission ( "categories" )) {
		?>
	<a href="index.php?action=categories"><?php translate("categories");?></a><br />
	<br />
	<?php
	}
	add_hook ( "content_type_list_entry" );
	?>
	<?php
} else {
	noperms ();
}
