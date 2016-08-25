<?php
if (! defined ( "ULICMS_ROOT" ))
	die ( "Bad Hacker!" );

$acl = new ACL ();

if ($acl->hasPermission ( "images" ) or $acl->hasPermission ( "videos" ) or $acl->hasPermission ( "audio" ) or $acl->hasPermission ( "files" )) {
	
	?>

<h2>
<?php translate("media");?>
</h2>
<strong><?php translate("please_select_filetype");?>
</strong>
<br />
<br />
<?php
	
	if ($acl->hasPermission ( "images" )) {
		?>
<a href="index.php?action=images"><?php translate("images");?>
</a>
<br />
<br />
<?php
	}
	?>
<?php

	if ($acl->hasPermission ( "files" )) {
		?>
<a href="index.php?action=files"><?php translate("files");?>
</a>
<br />
<br />
<?php
	}
	?>


<?php
	
	if ($acl->hasPermission ( "videos" )) {
		?>
<a href="index.php?action=videos"><?php translate("videos");?>
</a>
<br />
<br />
<?php
	}
	?>

<?php
	
	if ($acl->hasPermission ( "audio" )) {
		?>
<a href="index.php?action=audio"><?php translate("audio");?>
</a>
<?php
	}
} else {
	noperms ();
}