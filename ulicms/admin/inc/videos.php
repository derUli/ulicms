<?php
$acl = new ACL ();

$video_folder = ULICMS_ROOT . "/content/videos";
if (! is_dir ( $video_folder ))
	mkdir ( $video_folder );
if ($acl->hasPermission ( "videos" ) and isset ( $_REQUEST ["delete"] ) and get_request_method () == "POST") {
	$query = db_query ( "select ogg_file, webm_file, mp4_file from " . tbname ( "videos" ) . " where id = " . intval ( $_REQUEST ["delete"] ) );
	if (db_num_rows ( $query ) > 0) {
		
		// OGG
		$result = db_fetch_object ( $query );
		$filepath = ULICMS_ROOT . "/content/videos/" . basename ( $result->ogg_file );
		if (! empty ( $result->ogg_file ) and is_file ( $filepath )) {
			@unlink ( $filepath );
		}
		
		// WebM
		$result = db_fetch_object ( $query );
		$filepath = ULICMS_ROOT . "/content/videos/" . basename ( $result->webm_file );
		if (! empty ( $result->webm_file ) and is_file ( $filepath )) {
			@unlink ( $filepath );
		}
		
		// MP4
		$filepath = ULICMS_ROOT . "/content/videos/" . basename ( $result->mp4_file );
		if (! empty ( $result->mp4_file ) and is_file ( $filepath )) {
			
			@unlink ( $filepath );
		}
		
		db_query ( "DELETE FROM " . tbname ( "videos" ) . " where id = " . $_REQUEST ["delete"] );
	}
} else if ($acl->hasPermission ( "videos" ) and isset ( $_REQUEST ["update"] )) {
	$name = db_escape ( $_POST ["name"] );
	$id = intval ( $_POST ["id"] );
	$ogg_file = db_escape ( basename ( $_POST ["ogg_file"] ) );
	$webm_file = db_escape ( basename ( $_POST ["webm_file"] ) );
	$mp4_file = db_escape ( basename ( $_POST ["mp4_file"] ) );
	$width = intval ( $_POST ["width"] );
	$height = intval ( $_POST ["height"] );
	$updated = time ();
	$category_id = intval ( $_POST ["category"] );
	
	db_query ( "UPDATE " . tbname ( "videos" ) . " SET name='$name', ogg_file='$ogg_file', mp4_file='$mp4_file', webm_file='$webm_file', width=$width, height=$height, category_id = $category_id, `updated` = $updated where id = $id" ) or die ( db_error () );
} 

else if ($acl->hasPermission ( "videos" ) and isset ( $_FILES ) and isset ( $_REQUEST ["add"] )) {
	$mp4_file_value = "";
	// MP4
	if (! empty ( $_FILES ['mp4_file'] ['name'] )) {
		$mp4_file = time () . "-" . basename ( $_FILES ['mp4_file'] ['name'] );
		$mp4_type = $_FILES ['mp4_file'] ["type"];
		$mp4_allowed_mime_type = array (
				"video/mp4" 
		);
		if (in_array ( $mp4_type, $mp4_allowed_mime_type )) {
			$target = $video_folder . "/" . $mp4_file;
			if (move_uploaded_file ( $_FILES ['mp4_file'] ['tmp_name'], $target )) {
				$mp4_file_value = basename ( $mp4_file );
			}
		}
	}
	
	$ogg_file_value = "";
	// ogg
	if (! empty ( $_FILES ['ogg_file'] ['name'] )) {
		$ogg_file = time () . "-" . $_FILES ['ogg_file'] ['name'];
		$ogg_type = $_FILES ['ogg_file'] ["type"];
		$ogg_allowed_mime_type = array (
				"video/ogg",
				"application/ogg",
				"audio/ogg" 
		);
		if (in_array ( $ogg_type, $ogg_allowed_mime_type )) {
			$target = $video_folder . "/" . $ogg_file;
			if (move_uploaded_file ( $_FILES ['ogg_file'] ['tmp_name'], $target )) {
				$ogg_file_value = basename ( $ogg_file );
			}
		}
	}
	
	// WebM
	$webm_file_value = "";
	// webm
	if (! empty ( $_FILES ['webm_file'] ['name'] )) {
		$webm_file = time () . "-" . $_FILES ['webm_file'] ['name'];
		$webm_type = $_FILES ['webm_file'] ["type"];
		$webm_allowed_mime_type = array (
				"video/webm",
				"audio/webm",
				"application/webm" 
		);
		if (in_array ( $webm_type, $webm_allowed_mime_type )) {
			$target = $video_folder . "/" . $webm_file;
			if (move_uploaded_file ( $_FILES ['webm_file'] ['tmp_name'], $target )) {
				$webm_file_value = basename ( $webm_file );
			}
		}
	}
	
	$name = db_escape ( $_POST ["name"] );
	$category_id = intval ( $_POST ["category"] );
	$ogg_file_value = db_escape ( $ogg_file_value );
	$webm_file_value = db_escape ( $webm_file_value );
	$mp4_file_value = db_escape ( $mp4_file_value );
	
	$width = intval ( $_POST ["width"] );
	$height = intval ( $_POST ["height"] );
	$timestamp = time ();
	
	if (! empty ( $ogg_file_value ) or ! empty ( $mp4_file_value ) or ! empty ( $webm_file_value )) {
		db_query ( "INSERT INTO " . tbname ( "videos" ) . " (name, ogg_file, webm_file, mp4_file, width, height, created, category_id, `updated`) VALUES ('$name', '$ogg_file_value', '$webm_file_value',  '$mp4_file_value', $width, $height, $timestamp, $category_id, $timestamp);" ) or die ( db_error () );
	}
}

if (! isset ( $_SESSION ["filter_category"] )) {
	$_SESSION ["filter_category"] = 0;
}

if (isset ( $_GET ["filter_category"] )) {
	$_SESSION ["filter_category"] = intval ( $_GET ["filter_category"] );
}

$sql = "SELECT id, name, mp4_file, webm_file, ogg_file FROM " . tbname ( "videos" ) . " ";
if ($_SESSION ["filter_category"] > 0) {
	$sql .= " where category_id = " . $_SESSION ["filter_category"] . " ";
}
$sql .= " ORDER by id";

$all_videos = db_query ( $sql );

if ($acl->hasPermission ( "videos" )) {
	?>
<script type="text/javascript">
$(window).load(function(){
   $('#category').on('change', function (e) {
   var valueSelected = $('#category').val();
     location.replace("index.php?action=videos&filter_category=" + valueSelected)

   });

});
</script>
<h1>
<?php
	
	translate ( "videos" );
	?>
</h1>
<?php
	
	echo TRANSLATION_CATEGORY;
	?>
<?php

	echo categories::getHTMLSelect ( $_SESSION ["filter_category"], true );
	?>
<br />
<br />
<p>
	<a href="index.php?action=add_video">[<?php
	
	translate ( "upload_video" );
	?>]</a>
</p>
<table class="tablesorter">
	<thead>
		<tr>
			<th><?php
	
	translate ( "id" );
	?>
			</th>
			<th><?php
	
	translate ( "name" );
	?>
			</th>
			<th><?php
	
	translate ( "ogg_file" );
	?>
			</th>
			<th><?php
	
	translate ( "webm_file" );
	?>
			</th>
			<th><?php
	
	translate ( "mp4_file" );
	?>
			</th>
			<td></td>
			<td></td>
		</tr>
	
	
	<tbody>
	<?php
	while ( $row = db_fetch_object ( $all_videos ) ) {
		?>
		<tr id="dataset-<?php echo $row->id;?>">
			<td><?php
		
		echo $row->id;
		?>
			</td>
			<td><?php
		
		echo htmlspecialchars ( $row->name );
		?>
			</td>
			<td><?php
		
		echo htmlspecialchars ( basename ( $row->ogg_file ) );
		?>
			</td>
			<td><?php
		
		echo htmlspecialchars ( basename ( $row->webm_file ) );
		?>
			</td>
			<td><?php
		
		echo htmlspecialchars ( basename ( $row->mp4_file ) );
		?>
			</td>
			<td><a
				href="index.php?action=edit_video&id=<?php
		
		echo $row->id;
		?>"><img src="gfx/edit.png" class="mobile-big-image"
					alt="<?php
		
		translate ( "edit" );
		?>"
					title="<?php
		
		translate ( "edit" );
		?>"> </a></td>
			<td><form
					action="index.php?action=videos&delete=<?php
		
		echo $row->id;
		?>"
					method="post"
					onsubmit="return confirm('<?php
		
		translate ( "ASK_FOR_DELETE" );
		?>')"
					class="delete-form"><?php csrf_token_html();?><input type="image"
						src="gfx/delete.png" class="mobile-big-image"
						alt="<?php
		
		translate ( "delete" );
		?>"
						title="<?php
		
		translate ( "delete" );
		?>">
				</form></td>
		</tr>
		<?php
	}
	?>
	</tbody>
	</thead>
</table>

<script type="text/javascript">

var ajax_options = {
  success : function(responseText, statusText, xhr, $form){
  var action = $($form).attr("action");
  var id = url('?delete', action);
  var list_item_id = "dataset-" + id
  var tr = $("tr#" + list_item_id);
  $(tr).fadeOut();
  
  }
 
}

$("form.delete-form").ajaxForm(ajax_options); 
</script>

<?php
} else {
	noperms ();
}
