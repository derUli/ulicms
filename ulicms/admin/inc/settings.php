<?php
$acl = new ACL ();
if (defined ( "_SECURITY" )) {
	if ($acl->hasPermission ( "expert_settings" )) {
		
		$query = db_query ( "SELECT * FROM " . tbname ( "settings" ) . " ORDER BY name", $connection );
		if (db_num_rows ( $query ) > 0) {
			?>
			
<?php if($acl->hasPermission("expert_settings_create")){?>
<p>
	<a href="index.php?action=key_new"><?php translate("create_option");?></a>
</p>
<?php }?>

<table class="tablesorter">
	<thead>
		<tr style="font-weight: bold;">

			<th><?php translate("option");?></th>
			<th><?php translate("value");?></th>
			<?php if($acl->hasPermission("expert_settings_edit")){?>
			<td><?php translate("edit");?></td>
			<td><?php translate("delete");?></td>
			<?php }?>
		</tr>
	</thead>
	<tbody>
	<?php
			while ( $row = db_fetch_object ( $query ) ) {
				?>
		<?php
				
				echo '<tr id="dataset-' . htmlspecialchars ( $row->name ) . '">';
				echo "<td>" . htmlspecialchars ( $row->name, ENT_QUOTES, "UTF-8" ) . "</td>";
				echo "<td style=\"word-break:break-all;\">" . nl2br ( htmlspecialchars ( $row->value ) ) . "</td>";
				if ($acl->hasPermission ( "expert_settings_edit" )) {
					
					echo "<td style=\"text-align:center\">" . '<a href="index.php?action=key_edit&key=' . $row->id . '"><img src="gfx/edit.png" class="mobile-big-image" alt="' . get_translation ( "edit" ) . '" title="' . get_translation ( "edit" ) . '"></a></td>';
					echo "<td style=\"text-align:center;\">" . '<form action="index.php?action=key_delete&key=' . htmlspecialchars ( $row->name, ENT_QUOTES ) . '" onsubmit="return confirm(\'' . get_translation ( "ask_for_delete" ) . '\');" method="post" class="delete-form"><input type="image" src="gfx/delete.gif" class="mobile-big-image" alt="' . get_translation ( "delete" ) . '" title="' . get_translation ( "delete" ) . '">' . get_csrf_token_html () . '</form></td>';
				}
				echo '</tr>';
			}
		}
		?>
		 </tbody>
</table>

<script type="text/javascript">

var ajax_options = {
  success : function(responseText, statusText, xhr, $form){
  var action = $($form).attr("action");
  var id = url('?key', action);
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
}
