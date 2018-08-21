<h1><?php translate("create_gallery");?></h1>
<p>
	<a href="<?php

echo ModuleHelper::buildAdminURL("gallery2019");
?>"
		class="btn btn-default"><?php translate("back");?></a>
</p>
<?php echo ModuleHelper::buildMethodCallForm("GalleryController", "create");?>
<p>
	<strong><?php translate("title")?></strong> <br /> <input type="text"
		name="title" maxlength="200" value="" required>
</p>
<p>
	<button type="submit" class="btn btn-primary"><?php translate("save");?></button>
</p>
<?php echo ModuleHelper::endForm()?>