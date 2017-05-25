<?php
if (! defined ( "ULICMS_ROOT" )) {
	die ( "Dummer Hacker!" );
}
if (isset ( $_REQUEST ["standard"] )) {
	$standard = intval ( $_REQUEST ["standard"] );
	setconfig ( "default_acl_group", $standard );
}

$acl = new ACL ();
$groups = $acl->getAllGroups ();

$default_acl_group = intval ( Settings::get ( "default_acl_group" ) );

if (isset ( $_REQUEST ["sort"] ) and faster_in_array ( $_REQUEST ["sort"], array (
		"id",
		"name" 
) )) {
	$_SESSION ["grp_sort"] = $_REQUEST ["sort"];
}

if ($_SESSION ["grp_sort"] == "id") {
	if ($_SESSION ["sortDirection"] == "asc") {
		ksort ( $groups );
	} else if ($_SESSION ["sortDirection"] == "asc") {
		krsort ( $groups );
	}
} else if ($_SESSION ["grp_sort"] == "name") {
	if ($_SESSION ["sortDirection"] == "asc") {
		asort ( $groups );
	} else {
		arsort ( $groups );
	}
} else {
	ksort ( $groups );
}

?>
<?php if($acl->hasPermission("groups_create")){?>
<p>
	<a href="?action=groups&add=add"><?php translate("create_group");?> </a>
</p>
<?php }?>
<p><?php BackendHelper::formatDatasetCount(count($groups));?></p>

<?php

if (count ( $groups ) > 0) {
	?>
<div class="scroll">
	<table class="tablesorter">
		<thead>
			<tr>
				<th style="min-width: 100px;"><a
					href="?action=groups&sort=id&sort_direction=change"><strong><?php translate("id");?> </strong>
				</a></th>
				<th style="min-width: 200px;"><a
					href="?action=groups&sort=name&sort_direction=change"><strong><?php translate("name");?> </strong>
				</a></th>
			<?php if($acl->hasPermission("groups_edit")){?>
			<th><strong><?php translate("standard");?> </strong></th>

				<td></td>
				<td></td>
			<?php }?>
		</tr>
		</thead>
		<tbody>

	<?php
	
	foreach ( $groups as $id => $name ) {
		?>
		<tr id="dataset-<?php echo $id;?>">
				<td><?php
		
		echo $id;
		?>
			</td>
				<td><?php
		
		echo $name;
		?>
			</td>

<?php if($acl->hasPermission("groups_edit")){?>
			<td><?php
			
			if ($default_acl_group === $id) {
				?> <span style="color: green; font-weight: bold;"><?php translate("yes");?> </span> <?php
			} else {
				?> <a href="?action=groups&standard=<?php
				
				echo $id;
				?>"><span style="color: red; font-weight: bold;"
						onclick='return confirm("<?php
				
				echo str_ireplace ( "%name%", $name, get_translation ( "make_group_default" ) );
				?>")'><?php translate("no");?> </span> </a> <?php
			}
			?>
			</td>

				<td><a href="?action=groups&edit=<?php
			
			echo $id;
			?>"><img class="mobile-big-image" src="gfx/edit.png"
						alt="<?php
			
			translate ( "edit" );
			?>"
						title="<?php
			
			translate ( "edit" );
			?>"> </a></td>
				<td><form action="?action=groups&delete=<?php
			echo $id;
			?>"
						method="post"
						onsubmit="return confirm('<?php translate("ask_for_delete")?>');"
						class="delete-form"><?php csrf_token_html();?><input type="image"
							class="mobile-big-image" src="gfx/delete.gif"
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
}
?>
