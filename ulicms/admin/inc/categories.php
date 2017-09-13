<?php
$acl = new ACL ();
if (! is_admin () and ! $acl->hasPermission ( "categories" )) {
	noperms ();
} else {
	// Create
	if (isset ( $_REQUEST ["create"] )) {
		if (! empty ( $_REQUEST ["name"] )) {
			Categories::addCategory ( $_REQUEST ["name"], $_REQUEST ["description"] );
		}
	}
	
	// Create
	if (isset ( $_REQUEST ["update"] )) {
		if (! empty ( $_REQUEST ["name"] ) and ! empty ( $_REQUEST ["id"] )) {
			Categories::updateCategory ( intval ( $_REQUEST ["id"] ), $_REQUEST ["name"], $_REQUEST ["description"] );
		}
	}
	
	// Delete
	if (isset ( $_GET ["del"] ) && get_request_method () == "POST") {
		$del = intval ( $_GET ["del"] );
		if ($del != 1)
			Categories::deleteCategory ( $del );
	}
	
	include_once ULICMS_ROOT . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "string_functions.php";
	if (isset ( $_GET ["order"] ) and faster_in_array ( $_GET ["order"], array (
			"id",
			"name",
			"description",
			"created",
			"updated" 
	) )) {
		$order = db_escape ( $_GET ["order"] );
	} else {
		$order = "id";
	}
	
	$categories = Categories::getAllCategories ( $order );
	
	?>

			<?php
	if (! isset ( $_GET ["add"] ) and ! isset ( $_GET ["edit"] ) and $acl->hasPermission ( "categories_create" )) {
		?>

<h2><?php translate("categories");?></h2>
<p>
<?php translate("categories_infotext");?>
</p>

<p>
	<a href="?action=categories&add"><?php translate("create_category");?> </a>
</p>
<?php
	}
	?>
<p><?php BackendHelper::formatDatasetCount(count($categories));?></p>

<?php
	if (count ( $categories ) > 0 and ! isset ( $_GET ["add"] ) and ! isset ( $_GET ["edit"] )) {
		?>
<div class="scroll">
	<table class="tablesorter">
		<thead>
			<tr>
				<th style="min-width: 50px;"><a href="?action=categories&order=id"><?php translate("id");?> </a></th>

				<th style="min-width: 200px;"><a
					href="?action=categories&order=name"><?php translate("name");?></a></th>
				<th style="min-width: 200px;" class="hide-on-mobile"><a
					href="?action=categories&order=description"><?php translate("description");?> </a></th>
				<?php
		if ($acl->hasPermission ( "categories_edit" )) {
			?>
			<td></td>
				<td></td>
			<?php }?>
		</tr>

		</thead>
		<tbody>
	<?php
		
		foreach ( $categories as $category ) {
			?>
		<tr id="dataset-<?php echo $category["id"];?>">
				<td><?php
			
			echo $category ["id"];
			?></td>
				<td style="padding-right: 20px;"><?php
			
			echo real_htmlspecialchars ( $category ["name"] );
			?></td>
				<td style="padding-right: 20px;" class="hide-on-mobile"><?php
			
			echo nl2br ( real_htmlspecialchars ( $category ["description"] ) );
			?></td>
			<?php
			if ($acl->hasPermission ( "categories_edit" )) {
				?>
			<td style="text-align: center;"><a
					href="?action=categories&edit=<?php echo $category ["id"];?>"><img
						src="gfx/edit.png" class="mobile-big-image"
						alt="<?php translate("edit");?>"
						title="<?php translate("edit");?>"></a></td>
			<?php
				
				if ($category ["id"] != 1) {
					?>

			<td style="text-align: center;"><form
						action="?action=categories&del=<?php
					
					echo $category ["id"];
					?>"
						method="post" onsubmit="return confirm('Wirklich Löschen?')"
						class="delete-form"><?php csrf_token_html();?><input type="image"
							class="mobile-big-image" src="gfx/delete.gif"
							alt="<?php
					
					translate ( "delete" );
					?>"
							title="<?php
					
					translate ( "delete" );
					?>">
					</form></td>

				<?php
				} else {
					?>
			<td style="text-align: center;"><a href="#"
					onclick="alert('<?php translate("CANT_DELETE_CATEGORY_GENERAL");?>')"><img
						class="mobile-big-image" src="gfx/delete.gif"
						alt="<?php
					
					translate ( "delete" );
					?>"
						title="<?php
					
					translate ( "delete" );
					?>"> </a></td>
				<?php
				}
				?>
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
  var id = url('?del', action);
  var list_item_id = "dataset-" + id
  var tr = $("tr#" + list_item_id);
  $(tr).fadeOut();

  }

}

$("form.delete-form").ajaxForm(ajax_options);
</script>
<?php
	} else if (isset ( $_GET ["add"] )) {
		if ($acl->hasPermission ( "categories_create" )) {
			?>
<h2>
<?php translate("create_category");?>
</h2>
<form action="?action=categories" method="post">
<?php
			
			csrf_token_html ();
			?>
	<p>
	<?php translate("name");?>
		<input type="text" name="name" value="" required>

	</p>

	<p>
	<?php translate("description");?>
		<br />
		<textarea cols="50" name="description" rows="5" maxlength="255"></textarea>
	</p>
	<p>
		<button type="submit" name="create" class="btn btn-success"><?php translate("create");?></button>
	</p>


</form>

<?php
		} else {
			noperms ();
		}
	} else if (isset ( $_GET ["edit"] )) {
		
		if ($acl->hasPermission ( "categories_edit" )) {
			?>
<h2>
<?php translate("edit_category");?>
</h2>
<form action="?action=categories" method="post">
<?php
			
			csrf_token_html ();
			?>
	<input type="hidden" name="id"
		value="<?php echo intval($_GET["edit"])?>">
	<p>
	<?php translate("name");?>
		<input type="text" name="name" required
			value="<?php
			
			echo Categories::getCategoryById ( intval ( $_GET ["edit"] ) );
			?>">
	</p>

	<p>
	<?php translate("description");?>
		<br />
		<textarea cols="50" name="description" rows="5" maxlength="255"><?php
			echo htmlspecialchars ( Categories::getCategoryDescriptionById ( intval ( $_GET ["edit"] ) ) );
			?></textarea>
	</p>
	<p>
		<button type="submit" name="update" class="btn btn-success"><?php
			translate ( "save" );
			?></button>
	</p>
</form>
<?php
		} else {
			noperms ();
		}
	}
}
