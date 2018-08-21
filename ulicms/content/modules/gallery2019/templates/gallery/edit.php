<?php
use Gallery2019\Gallery;

$id = Request::getVar("id", 0, "int");
$model = new Gallery($id);
if ($id and $model->getID()) {
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
<h2><?php translate("images")?></h2>
<p>
	<a
		href="<?php echo ModuleHelper::buildActionURL("gallery_image_add", "gallery_id={$model->getId()}");?>"
		class="btn btn-default"><?php translate("add_image");?></a>
</p>
<div class="alert alert-warning">
	<p>Work in Progress</p>
</div>
<p>
	<button type="submit" class="btn btn-primary"><?php translate("save");?></button>
</p>
<?php echo ModuleHelper::endForm()?>
<?php
} else {
    noperms();
}
?>