<?php
$acl = new ACL ();
if ($acl->hasPermission ( "audio" ) and $acl->hasPermission ( "audio_create" )) {
	?>
<h1><?php translate ( "UPLOAD_AUDIO" );?>
</h1>
<form action="index.php?action=audio" method="post"
	enctype="multipart/form-data">
	<input type="hidden" name="add" value="add">
	<?php csrf_token_html ();?>
	<strong><?php translate ( "name" );?></strong> <br /> <input
		type="text" name="name" required value="" maxlength=255 /> <br /> <br />
	<strong><?php translate("category");?></strong><br />
	<?php
	echo Categories::getHTMLSelect ();
	?>
	<br /> <br /> <strong><?php echo translate ( "audio_ogg" );?>
	</strong><br /> <input name="ogg_file" type="file"><br /> <br /> <strong><?php echo translate ( "audio_mp3" );?>
	</strong><br /> <input name="mp3_file" type="file"><br /> <br /> <input
		type="submit" value="<?php translate ( "UPLOAD_audio" );?>">
</form>
<?php
} else {
	noperms ();
}
