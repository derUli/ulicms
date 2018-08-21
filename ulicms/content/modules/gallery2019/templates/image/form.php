<?php
use Gallery2019\Image;

$id = Request::getVar("id", null, "int");
$model = new Image($id);

$isEdit = (bool) $model->getID();
$gallery_id = Request::getVar("gallery_id", null, "int");
?>

<h1><?php translate($isEdit ? "edit_image" : "add_image");?></h1>
<p>
	<a
		href="<?php

echo ModuleHelper::buildActionURL("gallery_edit", "id={$gallery_id}");
?>"
		class="btn btn-default"><?php translate("back");?></a>
</p>

<?php

echo ModuleHelper::buildMethodCallForm("GalleryImageController", $isEdit ? "edit" : "create");
?>

<input type="hidden" name="id" value="<?php esc($model->getID());?>">
<input type="hidden" name="gallery_id"
	value="<?php esc($model->getGalleryId());?>">

<div class="alert alert-warning">
	<p>Work in Progress</p>
</div>
<p>
	<button type="submit" class="btn btn-primary"><?php translate($isEdit ? "edit_image" : "add_image"); ?></button>
<?php echo ModuleHelper::endForm()?>