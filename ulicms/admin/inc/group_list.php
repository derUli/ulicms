<?php
if (isset ( $_REQUEST ["standard"] )) {
	$standard = intval ( $_REQUEST ["standard"] );
	setconfig ( "default_acl_group", $standard );
}

$permissionChecker = new ACL ();
$groups = $permissionChecker->getAllGroups ();

$default_acl_group = intval ( Settings::get ( "default_acl_group" ) );

if (isset ( $_REQUEST ["sort"] ) and faster_in_array ( $_REQUEST ["sort"], array (
		"id",
		"name" 
) )) {
	$_SESSION ["grp_sort"] = $_REQUEST ["sort"];
}

?>
<?php if($permissionChecker->hasPermission("groups_create")){?>
<p>
	<a href="?action=groups&add=add" class="btn btn-default"><?php translate("create_group");?> </a>
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
				<th style="min-width: 100px;"><strong><?php translate("id");?></strong></th>
				<th style="min-width: 200px;"><strong><?php translate("name");?> </strong></th>
			<?php if($permissionChecker->hasPermission("groups_edit")){?>
			<th><strong><?php translate("standard");?> </strong></th>
				<td></td>
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
				<td><?php echo $id;?>
			</td>
				<td><?php echo $name;?>
			</td>

<?php if($permissionChecker->hasPermission("groups_edit")){?>
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
				<td><a
					href="<?php echo ModuleHelper::buildActionURL("admins", "admins_filter_group=".$id)?>"><img
						src="gfx/preview.gif" title="<?php translate("show_users");?>"
						alt="<?php translate("show_users");?>"></a></td>
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
						method="post" class="delete-form"><?php csrf_token_html();?><input
							type="image" class="mobile-big-image" src="gfx/delete.gif"
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

<?php
}

$translation = new JSTranslation ( array (
		"ask_for_delete" 
) );
$translation->render ();