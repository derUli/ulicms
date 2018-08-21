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
<?php echo ModuleHelper::buildMethodCallForm("Gallery2019Controller", "edit");?>

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
<?php
} else {
    noperms();
}
?>