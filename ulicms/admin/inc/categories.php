<?php
$acl = new ACL ();
if (! is_admin () and ! $acl -> hasPermission ("categories")){
     noperms ();
    }else{

     // Create
    if (isset ($_REQUEST ["create"])){
         if (! empty ($_REQUEST ["name"])){
             categories :: addCategory ($_REQUEST ["name"], $_REQUEST ["description"]);
             }
         }

     // Create
    if (isset ($_REQUEST ["update"])){
         if (! empty ($_REQUEST ["name"]) and ! empty ($_REQUEST ["id"])){
             categories :: updateCategory (intval ($_REQUEST ["id"]), $_REQUEST ["name"], $_REQUEST ["description"]);
             }
         }

     // Delete
    if (isset ($_GET ["del"]) && get_request_method() == "POST"){
         $del = intval ($_GET ["del"]);
         if ($del != 1)
             categories :: deleteCategory ($del);
         }

     include_once ULICMS_ROOT . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "string_functions.php";
     if (isset ($_GET ["order"]) and in_array ($_GET ["order"], array (
                    "id",
                     "name",
                     "description",
                     "created",
                     "updated"
                    )))
         $order = db_escape ($_GET ["order"]);
     else
         $order = "id";

     $categories = categories :: getAllCategories ($order);

     ?>

			<?php
	if (! isset ( $_GET ["add"] ) and ! isset ( $_GET ["edit"] )) {
		?>

<h2>
<?php
		
		echo TRANSLATION_CATEGORIES;
		?>
</h2>
<p>
<?php
		
		echo TRANSLATION_CATEGORIES_INFOTEXT;
		?>
</p>

<p>
	<a href="?action=categories&add"><?php
		
		echo TRANSLATION_CREATE_CATEGORY;
		?> </a>
</p>
<?php
	}
	?>

			<?php
	if (count ( $categories ) > 0 and ! isset ( $_GET ["add"] ) and ! isset ( $_GET ["edit"] )) {
		?>
<table class="tablesorter">

	<thead>
		<tr>
			<th style="min-width: 50px;"><a href="?action=categories&order=id"><?php
		
		echo TRANSLATION_ID;
		?> </a></th>

			<th style="min-width: 200px;"><a href="?action=categories&order=name"><?php
		
		echo TRANSLATION_NAME;
		?> </a></th>
			<th style="min-width: 200px;"><a
				href="?action=categories&order=description"><?php
		
		echo TRANSLATION_DESCRIPTION;
		?> </a></th>
			<td></td>
			<td></td>
		</tr>
	
	
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
			<td style="padding-right: 20px;"><?php
			
			echo nl2br ( real_htmlspecialchars ( $category ["description"] ) );
			?></td>
			<td style="text-align: center;"><a
				href="?action=categories&edit=<?php
			
			echo $category ["id"];
			?>"><img src="gfx/edit.png" class="mobile-big-image"
					alt="<?php
			
			echo TRANSLATION_EDIT;
			?>"
					title="<?php
			
			echo TRANSLATION_EDIT;
			?>"></td>
			<?php
			
			if ($category ["id"] != 1) {
				?>

			<td style="text-align: center;"><form action="?action=categories&del=<?php

                 echo $category ["id"];
                 ?>" method="post"
				onsubmit="return confirm('Wirklich Löschen?')" class="delete-form"><?php csrf_token_html();?><input type="image"
					class="mobile-big-image" src="gfx/delete.gif"
					alt="<?php

                 echo TRANSLATION_DELETE;
                 ?>"
					title="<?php

                 echo TRANSLATION_DELETE;
                 ?>"> </form></td>

				<?php
			} else {
				?>
			<td style="text-align: center;"><a href="#"
				onclick="alert('Die Allgemeine Kategorie kann nicht gelöscht werden!')"><img
					class="mobile-big-image" src="gfx/delete.gif"
					alt="<?php
				
				echo TRANSLATION_DELETE;
				?>"
					title="<?php
				
				echo TRANSLATION_DELETE;
				?>"> </a></td>
				<?php
			}
			?>

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
		?>
<h2>
<?php
		
		echo TRANSLATION_CREATE_CATEGORY;
		?>
</h2>
<form action="?action=categories" method="post">
<?php
		
		csrf_token_html ();
		?>
	<p>
	<?php
		
		echo TRANSLATION_NAME;
		?>
		<input type="text" required="true" name="name" required="true"
			value="">

	</p>

	<p>
	<?php
		
		echo TRANSLATION_DESCRIPTION;
		?>
		<br /> <textarea cols="50" name="description" rows="5" maxlength="255"></textarea>
	</p>
	<p>
		<input type="submit" name="create"
			value="<?php
		
		echo TRANSLATION_CREATE;
		?>">
	</p>


</form>

<?php
	} else if (isset ( $_GET ["edit"] )) {
		?>
<h2>
<?php
		
		echo TRANSLATION_EDIT_CATEGORY;
		?>
</h2>
<form action="?action=categories" method="post">
<?php
		
		csrf_token_html ();
		?>
	<input type="hidden" name="id"
		value="<?php echo intval($_GET["edit"])?>">
	<p>
	<?php
		
		echo TRANSLATION_NAME;
		?>
		<input type="text" name="name" required="true"
			value="<?php
		
		echo categories::getCategoryById ( intval ( $_GET ["edit"] ) );
		?>">
	</p>

	<p>
	<?php
		
		echo TRANSLATION_DESCRIPTION;
		?>
		<br /> <textarea cols="50" name="description" rows="5" maxlength="255"><?php
		
		echo htmlspecialchars ( categories::getCategoryDescriptionById ( intval ( $_GET ["edit"] ) ) );
		?></textarea>
	</p>
	<p>
		<input type="submit" name="update"
			value="<?php
		
		echo TRANSLATION_SAVE;
		?>">
	</p>
</form>

<?php
	}
	?>
         <?php
}
?>
