<?php
if (defined ( "_SECURITY" )) {
	include_once ULICMS_ROOT . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "string_functions.php";
	$acl = new ACL ();
	if (is_admin () or $acl->hasPermission ( "users" )) {
		if (empty ( $_GET ["order"] )) {
			$order = "username";
		} else if (faster_in_array ( $_GET ["order"], array (
				"id",
				"firstname",
				"lastname",
				"email",
				"group_id" 
		) )) {
			$order = basename ( $_GET ["order"] );
		} else {
			$order = "username";
		}
		$query = db_query ( "SELECT * FROM " . tbname ( "users" ) . " ORDER BY $order", $connection );
		if (db_num_rows ( $query )) {
			?>
<h2>
<?php translate("users");?>
</h2>
<?php if($acl->hasPermission("users_create")){?>
<p>
<?php translate("users_infotext");?>
	<br /> <br /> <a href="index.php?action=admin_new"><?php translate("create_user");?></a><br />
</p>
<?php }?>
<p><?php BackendHelper::formatDatasetCount(Database::getNumRows($query));?></p>

<div class="scroll">
	<table class="tablesorter">
		<thead>
			<tr style="font-weight: bold;">
				<th style="width: 40px;"><a href="index.php?action=admins&order=id">ID</a>
				</th>
				<th><span><a href="index.php?action=admins&order=username"><?php translate("username");?> </a>
				</span></th>
				<th class="hide-on-mobile"><a
					href="index.php?action=admins&order=lastname"><?php translate("lastname");?> </a></th>
				<th class="hide-on-mobile"><a
					href="index.php?action=admins&order=firstname"><?php translate("firstname");?> </a></th>
				<th class="hide-on-mobile"><a
					href="index.php?action=admins&order=email"><?php translate("email");?> </a></th>
				<th class="hide-on-mobile"><a
					href="index.php?action=admins&order=group_id"><?php translate("group");?> </a></th>

<?php if($acl->hasPermission("users_edit")){?>
			<td><?php translate ( "edit" );?></td>
				<td><span><?php translate("delete");?> </span></td>
			<?php }?>
		</tr>
		</thead>
		<tbody>
	<?php
			while ( $row = db_fetch_object ( $query ) ) {
				$group = $acl->getPermissionQueryResult ( $row->group_id );
				$group = $group ["name"];
				?>
		<?php
				
				echo '<tr id="dataset-' . $row->id . '">';
				echo "<td style=\"width:40px;\">" . $row->id . "</td>";
				echo "<td>";
				echo '<img src="' . get_gravatar ( $row->email, 26 ) . '" alt="Avatar von ' . real_htmlspecialchars ( $row->username ) . '"> ';
				echo real_htmlspecialchars ( $row->username ) . "</td>";
				echo "<td class=\"hide-on-mobile\">" . real_htmlspecialchars ( $row->lastname ) . "</td>";
				echo "<td class=\"hide-on-mobile\">" . real_htmlspecialchars ( $row->firstname ) . "</td>";
				echo "<td class=\"hide-on-mobile\">" . real_htmlspecialchars ( $row->email ) . "</td>";
				echo "<td class=\"hide-on-mobile\">" . real_htmlspecialchars ( $group ) . "</td>";
				if ($acl->hasPermission ( "users_edit" )) {
					echo "<td style='text-align:center;'>" . '<a href="index.php?action=admin_edit&admin=' . $row->id . '"><img class="mobile-big-image" src="gfx/edit.png" alt="' . get_translation ( "edit" ) . '" title="' . get_translation ( "edit" ) . '"></a></td>';
					
					if ($row->id == $_SESSION ["login_id"]) {
						echo "<td style='text-align:center;'><a href=\"#\" onclick=\"alert('" . get_translation ( "CANT_DELETE_ADMIN" ) . "')\"><img class=\"mobile-big-image\" src=\"gfx/delete.gif\" alt=\"" . get_translation ( "edit" ) . "\" title=\"" . get_translation ( "edit" ) . "\"></a></td>";
					} else {
						echo "<td style='text-align:center;'>" . '<form action="index.php?action=admin_delete&admin=' . $row->id . '" method="post" onsubmit="return confirm(\'' . get_translation ( "ask_for_delete" ) . '\');" class="delete-form">' . get_csrf_token_html () . '<input type="image" class="mobile-big-image" src="gfx/delete.gif"></form></td>';
					}
				}
				echo '</tr>';
			}
		}
		?>
		</tbody>
	</table>
</div>
<script type="text/javascript">
var ajax_options = {
  success : function(responseText, statusText, xhr, $form){
  var action = $($form).attr("action");
  var id = url('?admin', action);
  var list_item_id = "dataset-" + id
  var tr = $("tr#" + list_item_id);
  $(tr).fadeOut();

  }

}

$("form.delete-form").ajaxForm(ajax_options);
</script>
<br />
<br />
<?php
	} else {
		noperms ();
	}
	
	?>




	<?php
}
?>
