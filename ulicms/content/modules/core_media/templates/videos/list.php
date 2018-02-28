<?php
$acl = new ACL ();

$video_folder = ULICMS_DATA_STORAGE_ROOT . "/content/videos";
if (! is_dir ( $video_folder )) {
	mkdir ( $video_folder );
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
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("media");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h1>
<?php
	
	translate ( "videos" );
	?>
</h1>
<?php translate("category");?>
<?php
	
	echo Categories::getHTMLSelect ( $_SESSION ["filter_category"], true );
	?>
<br />
<br />
<?php if($acl->hasPermission("videos_create")){?>
<p>
	<a href="index.php?action=add_video" class="btn btn-default"><?php
		
		translate ( "upload_video" );
		?></a>
</p>
<?php }?>
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
	
	translate ( "ogg_file" );
	?>
			</th>
			<th class="hide-on-mobile"><?php
	
	translate ( "webm_file" );
	?>
			</th>
			<th class="hide-on-mobile"><?php
	
	translate ( "mp4_file" );
	?>
			</th>
			<?php if($acl->hasPermission("videos_edit")){?>
			<td></td>
			<td></td>
			<?php }?>
		</tr>
	</thead>
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
			<td class="hide-on-mobile"><?php
		
		echo htmlspecialchars ( basename ( $row->ogg_file ) );
		?>
			</td>
			<td class="hide-on-mobile"><?php
		
		echo htmlspecialchars ( basename ( $row->webm_file ) );
		?>
			</td>
			<td class="hide-on-mobile"><?php
		
		echo htmlspecialchars ( basename ( $row->mp4_file ) );
		?>
			</td>
			
			<?php if($acl->hasPermission("videos_edit")){?>
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
					action="?sClass=VideoController&sMethod=delete&delete=<?php echo $row->id;?>"
					method="post"
					onsubmit="return confirm('<?php translate ( "ASK_FOR_DELETE" );?>')"
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
