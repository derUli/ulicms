<?php
$acl = new ACL ();
if ($acl->hasPermission ( "videos" ) and $acl->hasPermission ( "videos_edit" )) {
	$id = intval ( $_REQUEST ["id"] );
	$query = db_query ( "SELECT * FROM " . tbname ( "videos" ) . " WHERE id = $id" );
	if (db_num_rows ( $query ) > 0) {
		$result = db_fetch_object ( $query );
		?><p>
	<a href="<?php echo ModuleHelper::buildActionURL("videos");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h1><?php translate ( "UPLOAD_VIDEO" );?></h1>
<form action="index.php?sClass=VideoController&sMethod=update"
	method="post">
<?php csrf_token_html ();?>
	<input type="hidden" name="id" value="<?php echo $result->id;?>"> <input
		type="hidden" name="update" value="update"> <strong><?php translate ( "name" );?>*
	</strong><br /> <input type="text" name="name" required="required"
		value="<?php echo htmlspecialchars ( $result->name );?>" maxlength=255 />
	<br /> <strong><?php translate("category");?>
	</strong><br />
	<?php echo Categories::getHTMLSelect ( $result->category_id );?>

	<br /> <br /> <strong><?php translate ( "video_ogg" );?>
	</strong><br /> <input name="ogg_file" type="text"
		value="<?php echo htmlspecialchars ( $result->ogg_file );?>"><br /> <strong><?php
		
		translate ( "video_webm" );
		?></strong><br /> <input name="webm_file" type="text"
		value="<?php echo htmlspecialchars ( $result->webm_file );?>"><br /> <strong><?php echo translate ( "video_mp4" );?>
	</strong><br /> <input name="mp4_file" type="text"
		value="<?php echo htmlspecialchars ( $result->mp4_file );?>"><br /> <strong><?php translate ( "width" );?>
	</strong><br /> <input type="number" name="width"
		value="<?php  echo $result->width;?>" step="1"> <br /> <strong><?php translate ( "height" );?>
	</strong><br /> <input type="number" name="height"
		value="<?php echo $result->height;?>" step="1"> <br /> <strong><?php translate ( "insert_this_code_into_a_page" );?>
	</strong><br /> <input type="text" name="code"
		value="[video id=<?php echo $result->id;?>]"
		onclick="this.focus();this.select();" readonly> <br />
	<button type="submit" class="btn btn-primary"><?php translate ( "save_changes" );?></button>


</form>
<?php
	} else {
		translate ( "video_not_found" );
	}
} else {
	noPerms ();
}
