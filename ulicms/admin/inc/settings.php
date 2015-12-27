<?php
$acl = new ACL ();
if (defined ( "_SECURITY" )) {
	if ($acl->hasPermission ( "expert_settings" )) {
		
		$query = db_query ( "SELECT * FROM " . tbname ( "settings" ) . " ORDER BY name", $connection );
		if (db_num_rows ( $query ) > 0) {
			?>
<br />
<a href="index.php?action=key_new"><?php
			
			echo TRANSLATION_CREATE_OPTION;
			?></a>
<br />
<br />

<table class="tablesorter">
	<thead>
		<tr style="font-weight: bold;">

			<th><?php
			
			echo TRANSLATION_OPTION;
			?></th>
			<th><?php
			
			echo TRANSLATION_VALUE;
			?></th>
			<td><?php
			
			echo TRANSLATION_EDIT;
			?></td>
			<td><?php
			
			echo TRANSLATION_DELETE;
			?></td>
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
				echo "<td style=\"text-align:center\">" . '<a href="index.php?action=key_edit&key=' . $row->id . '"><img src="gfx/edit.png" class="mobile-big-image" alt="' . TRANSLATION_EDIT . '" title="' . TRANSLATION_EDIT . '"></a></td>';
				echo "<td style=\"text-align:center;\">" . '<form action="index.php?action=key_delete&key=' . htmlspecialchars ( $row->name, ENT_QUOTES ) . '" onsubmit="return confirm(\'' . TRANSLATION_ASK_FOR_DELETE . '\');" method="post" class="delete-form"><input type="image" src="gfx/delete.gif" class="mobile-big-image" alt="' . TRANSLATION_DELETE . '" title="' . TRANSLATION_DELETE . '">' . get_csrf_token_html () . '</form></td>';
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
	
	?>




	<?php
}
?>
