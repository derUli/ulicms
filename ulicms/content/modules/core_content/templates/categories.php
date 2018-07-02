<?php
$acl = new ACL();
if (! is_admin() and ! $acl->hasPermission("categories")) {
    noPerms();
} else {
    include_once ULICMS_ROOT . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "string_functions.php";
    if (isset($_GET["order"]) and faster_in_array($_GET["order"], array(
        "id",
        "name",
        "description",
        "created",
        "updated"
    ))) {
        $order = db_escape($_GET["order"]);
    } else {
        $order = "id";
    }
    
    $categories = Categories::getAllCategories($order);
    ?>
<?php
    if (! isset($_GET["add"]) and ! isset($_GET["edit"]) and $acl->hasPermission("categories_create")) {
        ?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("contents");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h2><?php translate("categories");?></h2>
<p><?php translate("categories_infotext");?></p>
<p>
	<a href="?action=categories&add" class="btn btn-default"><?php translate("create_category");?></a>
</p>
<p><?php BackendHelper::formatDatasetCount(count($categories));?></p>
<?php
    }
    ?>
<?php
    if (count($categories) > 0 and ! isset($_GET["add"]) and ! isset($_GET["edit"])) {
        ?>
<div class="scroll">
	<table class="tablesorter">
		<thead>
			<tr>
				<th style="min-width: 50px;"><?php translate("id");?></th>

				<th style="min-width: 200px;"><?php translate("name");?></th>
				<th style="min-width: 200px;" class="hide-on-mobile"><?php translate("description");?></th>
				<?php
        if ($acl->hasPermission("categories_edit")) {
            ?>
			<td></td>
				<td></td>
			<?php }?>
		</tr>

		</thead>
		<tbody>
	<?php
        
        foreach ($categories as $category) {
            ?>
		<tr id="dataset-<?php echo $category["id"];?>">
				<td><?php
            
            echo $category["id"];
            ?></td>
				<td style="padding-right: 20px;"><?php
            
            echo real_htmlspecialchars($category["name"]);
            ?></td>
				<td style="padding-right: 20px;" class="hide-on-mobile"><?php
            
            echo nl2br(real_htmlspecialchars($category["description"]));
            ?></td>
			<?php
            if ($acl->hasPermission("categories_edit")) {
                ?>
			<td style="text-align: center;"><a
					href="?action=categories&edit=<?php echo $category ["id"];?>"><img
						src="gfx/edit.png" class="mobile-big-image"
						alt="<?php translate("edit");?>"
						title="<?php translate("edit");?>"></a></td>
			<?php
                
                if ($category["id"] != 1) {
                    ?>
<!-- FIXME. "Wirklich löschen?" ist hart gecodet -->
				<td style="text-align: center;"><form
						action="?sClass=CategoryController&sMethod=delete&del=<?php
                    
                    echo $category["id"];
                    ?>"
						method="post" onsubmit="return confirm('Wirklich Löschen?')"
						class="delete-form"><?php csrf_token_html();?><input type="image"
							class="mobile-big-image" src="gfx/delete.gif"
							alt="<?php
                    
                    translate("delete");
                    ?>"
							title="<?php
                    
                    translate("delete");
                    ?>">
					</form></td>

				<?php
                } else {
                    ?>
			<td style="text-align: center;"><a href="#"
					onclick="alert('<?php translate("CANT_DELETE_CATEGORY_GENERAL");?>')"><img
						class="mobile-big-image" src="gfx/delete.gif"
						alt="<?php
                    
                    translate("delete");
                    ?>"
						title="<?php
                    
                    translate("delete");
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
<?php
        enqueueScriptFile(ModuleHelper::buildRessourcePath("core_content", "js/categories.js"));
        combinedScriptHtml();
        ?>
<?php
    } else if (isset($_GET["add"])) {
        if ($acl->hasPermission("categories_create")) {
            ?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("categories");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h2><?php translate("create_category");?></h2>
<?php echo ModuleHelper::buildMethodCallForm("CategoryController", "create");?>
<p>
	<strong><?php translate("name");?>*</strong> <input type="text"
		name="name" value="" required>

</p>

<p>
	<strong><?php translate("description");?></strong> <br />
	<textarea cols="50" name="description" rows="5" maxlength="255"></textarea>
</p>
<p>
	<button type="submit" name="create" class="btn btn-primary"><?php translate("create");?></button>
</p>


</form>

<?php
        } else {
            noPerms();
        }
    } else if (isset($_GET["edit"])) {
        if ($acl->hasPermission("categories_edit")) {
            ?><p>
	<a href="<?php echo ModuleHelper::buildActionURL("categories");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h2><?php translate("edit_category");?></h2>
<?php echo ModuleHelper::buildMethodCallForm("CategoryController", "update");?>
<input type="hidden" name="id"
	value="<?php echo intval($_GET["edit"])?>">
<p>
	<strong><?php translate("name");?>*</strong> <input type="text"
		name="name" required
		value="<?php
            
            echo Categories::getCategoryById(intval($_GET["edit"]));
            ?>">
</p>
<p>

	<strong><?php translate("description");?></strong> <br />
	<textarea cols="50" name="description" rows="5" maxlength="255"><?php
            echo htmlspecialchars(Categories::getCategoryDescriptionById(intval($_GET["edit"])));
            ?></textarea>
</p>
<p>
	<button type="submit" name="update" class="btn btn-primary"><?php
            translate("save");
            ?></button>
</p>
</form>
<?php
        } else {
            noPerms();
        }
    }
}
