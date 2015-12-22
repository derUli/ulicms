<?php
$acl = new ACL ();
if (! $acl->hasPermission ( "upload_patches" )) {
	noperms ();
} else {
	$redirect = false;
	
	if (isset ( $_POST ["upload_patch"] ) and isset ( $_FILES ['file'] ['tmp_name'] ) and endsWith ( $_FILES ['file'] ['name'], ".zip" )) {
		$pkg = new packageManager ();
		if ($pkg->installPatch ( $_POST ["name"], $_POST ["description"], $_FILES ['file'] ['tmp_name'] )) {
			
			$redirect = true;
		}
	}
	?>
<?php if($redirect){ ?>
<script type="text/javascript">
window.location.replace("index.php?action=modules#installed_patches_a");
</script>
<?php }?>
<h1><?php translate("install_patch_from_file"); ?></h1>
<form enctype="multipart/form-data"
	action="index.php?action=upload_patches" method="POST">
<?php csrf_token_html();?>
<p>
		<strong><?php translate("name");?></strong> <br /> <input type="text"
			name="name" value="" required="true" />
	</p>
	<p>
		<strong><?php translate("description");?></strong> <br /> <input
			type="text" name="description" value="" required="true" />
	</p>

	<p>
		<strong><?php translate("file");?></strong> <br /> <input name="file"
			type="file" required="true" />
	</p>
	<input type="submit" name="upload_patch"
		value="<?php translate("install_patch");?>" />
</form>
<?php }?>
