<?php
// @FIXME: Das SQL hier muss in einen Controller verschoben werden.
$acl = new ACL ();
if ($acl->hasPermission ( "banners" )) {
	if (! isset ( $_SESSION ["filter_category"] )) {
		$_SESSION ["filter_category"] = 0;
	}
	if (isset ( $_GET ["filter_category"] )) {
		$_SESSION ["filter_category"] = intval ( $_GET ["filter_category"] );
	}
	$sql = "SELECT * FROM " . tbname ( "banner" ) . " ";
	if ($_SESSION ["filter_category"] == 0) {
		$sql .= "WHERE 1=1 ";
	} else {
		$sql .= "WHERE category=" . $_SESSION ["filter_category"] . " ";
	}
	$sql .= "ORDER BY id";
	$query = db_query ( $sql );
	?>
<script type="text/javascript">
$(window).load(function(){
   $('#category').on('change', function (e) {
   var valueSelected = $('#category').val();
     location.replace("index.php?action=banner&filter_category=" + valueSelected)
   });
});
</script>

<h2><?php translate("advertisements"); ?></h2>
<p>
<?php translate("advertisement_infotext");?>
	<?php
	if ($acl->hasPermission ( "banners_create" )) {
		?><br /> <br /> <a href="index.php?action=banner_new"><?php translate("add_advertisement");?>
	</a><br />
	<?php }?>
</p>
<p><?php translate("category");?>
<?php
	echo Categories::getHTMLSelect ( $_SESSION ["filter_category"], true );
	?>
</p>

<p><?php BackendHelper::formatDatasetCount(Database::getNumRows($query));?></p>
<div class="scroll">
	<table class="tablesorter">
		<thead>
			<tr style="font-weight: bold;">
				<th><?php translate ( "advertisements" );?>
			</th>
				<th><?php translate("language");?>
			</th>
			<?php if ($acl->hasPermission ( "banners_edit" )) {?>
			<td><?php translate ( "edit" );?>
			</td>
				<td><?php translate ( "delete" );?>
			</td>
				<?php }?>
		</tr>
		</thead>
		<tbody>
	<?php
	if (db_num_rows ( $query ) > 0) {
		while ( $row = db_fetch_object ( $query ) ) {
			?>
			<?php
			echo '<tr id="dataset-' . $row->id . '">';
			if ($row->type == "gif") {
				$link_url = Template::getEscape ( $row->link_url );
				$image_url = Template::getEscape ( $row->image_url );
				$name = Template::getEscape ( $row->name );
				echo '<td><a href="' . $link_url . '" target="_blank"><img src="' . $image_url . '" title="' . $name . '" alt="' . $name . '" border=0></a></td>';
			} else {
				echo '<td>' . Template::getEscape ( $row->html ) . '</td>';
			}
			if ($row->language == "all") {
				echo '<td>Alle</td>';
			} else {
				echo '<td>' . getLanguageNameByCode ( $row->language ) . "</td>";
			}
			if ($acl->hasPermission ( "banners_edit" )) {
				echo "<td style='text-align:center;'>" . '<a href="index.php?action=banner_edit&banner=' . $row->id . '"><img class="mobile-big-image" src="gfx/edit.png" alt="' . get_translation ( "edit" ) . '" title="' . get_translation ( "edit" ) . '"></a></td>';
				echo "<td style='text-align:center;'>" . '<form action="index.php?action=banner_delete&banner=' . $row->id . '" method="post" onsubmit="return confirm(\'Wirklich löschen?\');" class="delete-form">' . get_csrf_token_html () . '<input type="image" class="mobile-big-image" src="gfx/delete.gif" title="' . get_translation ( "delete" ) . '"></form></td>';
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
  var id = url('?banner', action);
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
