<?php
$permissionChecker = new ACL ();
if ($permissionChecker->hasPermission ( "pages" ) or $permissionChecker->hasPermission ( "banners" ) or $permissionChecker->hasPermission ( "categories" ) or $permissionChecker->hasPermission ( "export" ) or $permissionChecker->hasPermission ( "forms" )) {
	?>
<h2><?php translate("contents");?></h2>
<p>
	<strong><?php translate("select_content_type");?> </strong><br /> <br />
	<?php
	
	if ($permissionChecker->hasPermission ( "pages" )) {
		?>
	<a href="index.php?action=pages"><?php translate("pages");?></a><br />
	<br />
	<?php
	}
	if ($permissionChecker->hasPermission ( "forms" )) {
		?><a href='?action=forms'><?php
		
		translate ( "forms" );
		?></a> <br />
				<?php
	}
	if ($permissionChecker->hasPermission ( "banners" )) {
		?>
	<a href="index.php?action=banner"><?php translate("advertisements");?></a><br />

	<?php
	}
	if ($permissionChecker->hasPermission ( "categories" )) {
		?>
	<a href="index.php?action=categories"><?php translate("categories");?></a><br />
	<br />
	<?php
	}
	do_event ( "content_type_list_entry" );
	?>
	<?php
} else {
	noPerms ();
}
