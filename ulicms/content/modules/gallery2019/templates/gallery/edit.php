<?php
use Gallery2019\Gallery;

$id = Request::getVar("id", 0, "int");
$model = new Gallery($id);
if ($id and $model->getID()) {
    $images = $model->getImages();
    ?>
<h1><?php translate("edit_gallery");?></h1>
<p>
	<a
		href="<?php
    
    echo ModuleHelper::buildAdminURL("gallery2019");
    ?>"
		class="btn btn-default"><?php translate("back");?></a>
</p>
<?php if(Request::getVar("save")){?>
<div class="alert alert-success alert-dismissable fade in">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<?php translate("changes_was_saved")?>
		</div>
<?php }?>
<?php echo ModuleHelper::buildMethodCallForm("GalleryController", "edit");?>

<input type="hidden" name="id" value="<?php esc($id);?>">
<p>
	<strong><?php translate("title")?></strong> <br /> <input type="text"
		name="title" maxlength="200" value="<?php esc($model->getTitle());?>"
		required>
</p>
<p>
	<button type="submit" class="btn btn-primary"><?php translate("save");?></button>
</p>
<?php echo ModuleHelper::endForm()?>
<h2><?php translate("images")?></h2>
<p>
	<a
		href="<?php echo ModuleHelper::buildActionURL("gallery_image_add", "gallery_id={$model->getId()}");?>"
		class="btn btn-default"><?php translate("add_image");?></a>
</p>

<table class="tablesorter">
	<thead>
		<tr>
			<th><?php translate("position");?></th>
			<th><?php translate("image");?></th>
			<th><?php translate("description");?></th>
			<td></td>
			<td></td>
		</tr>
	</thead>
	<tbody>
	<?php foreach($images as $image){?>
	<tr>
			<td class="text-right"><?php esc($image->getOrder());?></td>
			<td data-sort-value="<?php esc($image->getPath());?>"><img
				class="img-thumbnail" src="<?php esc($image->getPath());?>"
				title="<?php esc($image->getPath());?>"></td>
			<td><?php esc($image->getDescription())?></td>
			<td class="text-center"><a
				href="<?php echo ModuleHelper::buildActionURL("gallery_image_edit", "id=".$image->getID());?>"><img
					src="gfx/edit.png" class="mobile-big-image"
					alt="<?php translate("edit");?>" title="<?php translate("edit");?>"></a></td>
			<td class="text-center">
    				<?php
        echo ModuleHelper::deleteButton(ModuleHelper::buildMethodCallUrl("GalleryImageController", "delete"), array(
            "id" => $image->getID()
        ));
        ?>
    			</td>
		</tr>
	<?php } ?>
	</tbody>
</table>

<?php
} else {
    noperms();
}
?>
<?php
$translation = new JSTranslation();
$translation->addKey("ask_for_delete");
$translation->render();
?>