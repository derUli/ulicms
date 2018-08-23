<?php
use Gallery2019\Image;

$id = Request::getVar("id", null, "int");
$model = new Image($id);

$isEdit = (bool) $model->getID();
$gallery_id = $isEdit ? $model->getGalleryId() : Request::getVar("gallery_id", null, "int");
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
<input type="hidden" name="gallery_id" value="<?php esc($gallery_id);?>">

<?php
$path = new FileImage();
$path->name = "path";
$path->title = "file";
$path->required = true;
echo $path->render($model->getPath());

$description = new MultilineTextField();
$description->name = "description";
$description->title = "description";
echo $description->render($model->getDescription());

$order = new NumberField();
$order->name = "position";
$order->title = "position";
$order->htmlAttributes = array(
    "step" => 1
);
echo $order->render($model->getOrder());
?>

<p>
	<button type="submit" class="btn btn-primary"><?php translate($isEdit ? "edit_image" : "add_image"); ?></button>
</p>
<?php echo ModuleHelper::endForm()?>

<?php
enqueueScriptFile(ModuleHelper::buildRessourcePath("gallery2019", "js/backend.js"));
combinedScriptHtml();
?>