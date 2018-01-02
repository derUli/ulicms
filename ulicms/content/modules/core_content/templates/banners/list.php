<?php
$acl = new ACL ();
if ($acl->hasPermission ( "banners" )) {
	if (! isset ( $_SESSION ["filter_category"] )) {
		$_SESSION ["filter_category"] = 0;
	}
	if (isset ( $_GET ["filter_category"] )) {
		$_SESSION ["filter_category"] = intval ( $_GET ["filter_category"] );
	}
	if ($_SESSION ["filter_category"] == 0) {
		$banners = Banners::getAll ();
	} else {
		$banners = Banners::getByCategory ( $_SESSION ["filter_category"] );
	}
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

<p><?php BackendHelper::formatDatasetCount(count ( $banners ));?></p>
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
	if (count ( $banners ) > 0) {
		foreach ( $banners as $banner ) {
			?>
			<?php
			echo '<tr id="dataset-' . $banner->id . '">';
			if ($banner->getType () == "gif") {
				$link_url = Template::getEscape ( $banner->link_url );
				$image_url = Template::getEscape ( $banner->image_url );
				$name = Template::getEscape ( $banner->name );
				echo '<td><a href="' . $link_url . '" target="_blank"><img src="' . $image_url . '" title="' . $name . '" alt="' . $name . '" border=0></a></td>';
			} else {
				echo '<td>' . Template::getEscape ( $banner->html ) . '</td>';
			}
			if ($banner->language == "all") {
				echo '<td>Alle</td>';
			} else {
				echo '<td>' . getLanguageNameByCode ( $banner->language ) . "</td>";
			}
			if ($acl->hasPermission ( "banners_edit" )) {
				echo "<td style='text-align:center;'>" . '<a href="index.php?action=banner_edit&banner=' . $banner->id . '"><img class="mobile-big-image" src="gfx/edit.png" alt="' . get_translation ( "edit" ) . '" title="' . get_translation ( "edit" ) . '"></a></td>';
				echo "<td style='text-align:center;'>" . '<form action="index.php?sClass=BannerController&sMethod=delete&banner=' . $banner->id . '" method="post" onsubmit="return confirm(\'Wirklich löschen?\');" class="delete-form">' . get_csrf_token_html () . '<input type="image" class="mobile-big-image" src="gfx/delete.gif" title="' . get_translation ( "delete" ) . '"></form></td>';
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
