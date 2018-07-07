<?php
$acl = new ACL ();
if ($acl->hasPermission ( "audio" ) and $acl->hasPermission ( "audio_create" )) {
	?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("audio");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h1><?php translate ( "UPLOAD_AUDIO" );?>
</h1>
<form action="index.php?sClass=AudioController&sMethod=create"
	method="post" enctype="multipart/form-data">
	<input type="hidden" name="add" value="add">
	<?php csrf_token_html ();?>
	<strong><?php translate ( "name" );?>*</strong> <br /> <input
		type="text" name="name" required value="" maxlength=255 /> <br />
	<strong><?php translate("category");?></strong><br />
	<?php
	echo Categories::getHTMLSelect ();
	?>
	<br /> <br /> <strong><?php echo translate ( "audio_ogg" );?>
	</strong><br /> <input name="ogg_file" type="file"><br /> <strong><?php echo translate ( "audio_mp3" );?>
	</strong><br /> <input name="mp3_file" type="file"><br />
	<button type="submit" class="btn btn-primary"><?php translate ( "UPLOAD_audio" );?></button>
</form>
<?php
} else {
	noPerms ();
}
