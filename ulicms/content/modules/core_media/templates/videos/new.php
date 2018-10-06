<?php
$permissionChecker = new ACL ();
if ($permissionChecker->hasPermission ( "videos" ) and $permissionChecker->hasPermission ( "videos_create" )) {
	?><p>
	<a href="<?php echo ModuleHelper::buildActionURL("videos");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h1><?php translate ( "UPLOAD_VIDEO" );?></h1>
<form action="index.php?sClass=VideoController&sMethod=create"
	method="post" enctype="multipart/form-data">
	<input type="hidden" name="add" value="add">
	<?php csrf_token_html ();?>
	<strong><?php translate ( "name" );?>*
	</strong><br /> <input type="text" name="name" value="" maxlength="255"
		required /> <br /> <strong><?php translate("category");?>
	</strong><br />
	<?php echo Categories::getHTMLSelect ();?>

	<br /> <br /> <strong><?php echo translate ( "video_ogg" );?>
	</strong><br /> <input name="ogg_file" type="file"><br /> <strong><?php echo translate ( "video_webm" );?>
	</strong><br /> <input name="webm_file" type="file"><br /> <strong><?php echo translate ( "video_mp4" );?>
	</strong><br /> <input name="mp4_file" type="file"><br /> <strong><?php translate ( "width" );?>
	</strong><br /> <input type="number" name="width" value="1280" step="1">
	<br /> <strong><?php translate ( "height" );?></strong><br /> <input
		type="number" name="height" value="720" step="1"> <br />
	<button type="submit" class="btn btn-primary"><?php translate ( "UPLOAD_VIDEO" );?></button>
</form>
<?php
} else {
	noPerms ();
}
