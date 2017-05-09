<?php
$acl = new ACL ();
$audio_folder = ULICMS_ROOT . "/content/audio";
if (! is_dir ( $audio_folder ))
	mkdir ( $audio_folder );
if ($acl->hasPermission ( "audio" ) and isset ( $_REQUEST ["delete"] ) and get_request_method () == "POST") {
	$query = db_query ( "select ogg_file, mp3_file from " . tbname ( "audio" ) . " where id = " . intval ( $_REQUEST ["delete"] ) );
	if (db_num_rows ( $query ) > 0) {
		$result = db_fetch_object ( $query );
		$filepath = ULICMS_ROOT . "/content/audio/" . basename ( $result->ogg_file );
		if (! empty ( $result->ogg_file ) and is_file ( $filepath )) {
			@unlink ( $filepath );
		}
		
		$filepath = ULICMS_ROOT . "/content/audio/" . basename ( $result->mp3_file );
		if (! empty ( $result->mp3_file ) and is_file ( $filepath )) {
			
			@unlink ( $filepath );
		}
		
		db_query ( "DELETE FROM " . tbname ( "audio" ) . " where id = " . $_REQUEST ["delete"] );
	}
} else if ($acl->hasPermission ( "audio" ) and isset ( $_REQUEST ["update"] )) {
	$name = db_escape ( $_POST ["name"] );
	$id = intval ( $_POST ["id"] );
	$ogg_file = db_escape ( basename ( $_POST ["ogg_file"] ) );
	$mp3_file = db_escape ( basename ( $_POST ["mp3_file"] ) );
	$updated = time ();
	$category_id = intval ( $_POST ["category"] );
	
	db_query ( "UPDATE " . tbname ( "audio" ) . " SET name='$name', ogg_file='$ogg_file', mp3_file='$mp3_file', category_id = $category_id, `updated` = $updated where id = $id" ) or die ( db_error () );
} 

else if ($acl->hasPermission ( "audio" ) and isset ( $_FILES ) and isset ( $_REQUEST ["add"] )) {
	$mp3_file_value = "";
	// mp3
	if (! empty ( $_FILES ['mp3_file'] ['name'] )) {
		$mp3_file = time () . "-" . basename ( $_FILES ['mp3_file'] ['name'] );
		$mp3_type = $_FILES ['mp3_file'] ["type"];
		$mp3_allowed_mime_type = array (
				"audio/mp3",
				"audio/mpeg3",
				"audio/x-mpeg-3",
				"video/mpeg",
				"video/x-mpeg",
				"audio/mpeg" 
		);
		if (in_array ( $mp3_type, $mp3_allowed_mime_type )) {
			$target = $audio_folder . "/" . $mp3_file;
			if (move_uploaded_file ( $_FILES ['mp3_file'] ['tmp_name'], $target )) {
				$mp3_file_value = basename ( $mp3_file );
			}
		}
	}
	
	$ogg_file_value = "";
	// ogg
	if (! empty ( $_FILES ['ogg_file'] ['name'] )) {
		$ogg_file = time () . "-" . $_FILES ['ogg_file'] ['name'];
		$ogg_type = $_FILES ['ogg_file'] ["type"];
		$ogg_allowed_mime_type = array (
				"audio/ogg",
				"application/ogg",
				"video/ogg" 
		);
		if (in_array ( $ogg_type, $ogg_allowed_mime_type )) {
			$target = $audio_folder . "/" . $ogg_file;
			if (move_uploaded_file ( $_FILES ['ogg_file'] ['tmp_name'], $target )) {
				$ogg_file_value = basename ( $ogg_file );
			}
		}
	}
	
	$name = db_escape ( $_POST ["name"] );
	$category_id = intval ( $_POST ["category"] );
	$ogg_file_value = db_escape ( $ogg_file_value );
	$mp3_file_value = db_escape ( $mp3_file_value );
	$timestamp = time ();
	
	if (! empty ( $ogg_file_value ) or ! empty ( $mp3_file_value )) {
		db_query ( "INSERT INTO " . tbname ( "audio" ) . " (name, ogg_file, mp3_file, created, category_id, `updated`) VALUES ('$name', '$ogg_file_value', '$mp3_file_value', $timestamp, $category_id, $timestamp);" ) or die ( db_error () );
	}
}

if (! isset ( $_SESSION ["filter_category"] )) {
	$_SESSION ["filter_category"] = 0;
}

if (isset ( $_GET ["filter_category"] )) {
	$_SESSION ["filter_category"] = intval ( $_GET ["filter_category"] );
}

$sql = "SELECT id, name, mp3_file, ogg_file FROM " . tbname ( "audio" ) . " ";
if ($_SESSION ["filter_category"] > 0) {
	$sql .= " where category_id = " . $_SESSION ["filter_category"] . " ";
}
$sql .= " ORDER by id";

$all_audio = db_query ( $sql );

if ($acl->hasPermission ( "audio" )) {
	?>
<script type="text/javascript">
$(window).load(function(){
   $('#category').on('change', function (e) {
   var valueSelected = $('#category').val();
     location.replace("index.php?action=audio&filter_category=" + valueSelected)

   });

});
</script>
<h1>
<?php
	
	translate ( "audio" );
	?>
</h1>
<?php translate("category");?>
<?php

	echo categories::getHTMLSelect ( $_SESSION ["filter_category"], true );
	?>
<br />
<br />
<?php if($acl->hasPermission("audio_create")){?>
<p>
	<a href="index.php?action=add_audio">[<?php
		
		translate ( "upload_audio" );
		?>]</a>
</p>
<?php }?>
<div class="scroll">
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
				<th class="hide-on-mobile"><?php
	
	translate ( "OGG_FILE" );
	?>
			</th>
				<th class="hide-on-mobile"><?php
	
	translate ( "MP3_FILE" );
	?>
			</th>

<?php if($acl->hasPermission("audio_edit")){?>
			<td></td>
				<td></td>
			<?php }?>
		</tr>

		</thead>
		<tbody>
	<?php
	while ( $row = db_fetch_object ( $all_audio ) ) {
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
				<td class="hide-on-mobile"><?php
		
		echo htmlspecialchars ( basename ( $row->ogg_file ) );
		?>
			</td>
				<td class="hide-on-mobile"><?php
		
		echo htmlspecialchars ( basename ( $row->mp3_file ) );
		?>
			</td>

	<?php if($acl->hasPermission("audio_edit")){?>
			<td><a
					href="index.php?action=edit_audio&id=<?php
			
			echo $row->id;
			?>"><img src="gfx/edit.png" class="mobile-big-image"
						alt="<?php
			
			translate ( "edit" );
			?>"
						title="<?php
			
			translate ( "edit" );
			?>"> </a></td>
				<td><form
						action="index.php?action=audio&delete=<?php
			
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
				<?php }?>
		</tr>
		<?php
	}
	?>
	</tbody>

	</table>
</div>
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
