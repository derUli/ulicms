<?php
$acl = new ACL ();
if ($acl->hasPermission ( "audio" )) {
	
	$id = intval ( $_REQUEST ["id"] );
	$query = db_query ( "SELECT * FROM " . tbname ( "audio" ) . " WHERE id = $id" );
	if (db_num_rows ( $query ) > 0) {
		$result = db_fetch_object ( $query );
		?>
<h1><?php
		
		translate ( "UPLOAD_AUDIO" );
		?></h1>
<form action="index.php?action=audio" method="post">
<?php
		
		csrf_token_html ();
		?>
<input type="hidden" name="id"
		value="<?php
		
		echo $result->id;
		?>"> <input type="hidden" name="update" value="update"> <strong><?php
		
		translate ( "name" );
		?></strong><br /> <input type="text" name="name" required="true"
		value="<?php
		
		echo htmlspecialchars ( $result->name );
		?>"
		maxlength=255 /> <br /> <br /> <strong><?php
		
		echo TRANSLATION_CATEGORY;
		?></strong><br />
<?php
		echo categories::getHTMLSelect ( $result->category_id );
		?>

<br /> <br /> <strong><?php
		
		echo translate ( "audio_ogg" );
		?></strong><br /> <input name="ogg_file" type="text"
		value="<?php
		
		echo htmlspecialchars ( $result->ogg_file );
		?>"><br /> <br /> <strong><?php
		
		echo translate ( "audio_mp3" );
		?></strong><br /> <input name="mp3_file" type="text"
		value="<?php
		
		echo htmlspecialchars ( $result->mp3_file );
		?>"><br /> <br /> <strong><?php
		
		translate ( "insert_this_code_into_a_page" );
		?></strong><br /> <input type="text" name="code"
		value="[audio id=<?php
		
		echo $result->id;
		?>]"
		onclick="this.focus();this.select();" readonly> <br /> <br /> <input
		type="submit" value="<?php
		
		translate ( "SAVE_CHANGES" );
		?>">
</form>
<?php
	} else {
		echo "audio not found!";
	}
} else {
	noperms ();
}
