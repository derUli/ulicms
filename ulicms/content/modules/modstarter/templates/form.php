<?php
$sources = array (
		"",
		"pkgsrc",
		"extend",
		"local",
		"core" 
);
// TODO: List of all hooks
$hooks = array (
		"uninstall",
		"clearCache" 
);
$model = ViewBag::get ( "model" ) ? ViewBag::get ( "model" ) : new ModStarterProjectViewModel ();

$action = $model->edit ? "update" : "create";
$headline = $model->edit ? "edit_module" : "create_module";
?>
<h1><?php translate($headline);?></h1>
<?php echo ModuleHelper::buildMethodCallForm("ModStarter", $action);?>
<p>
	<a href="<?php echo ModuleHelper::buildAdminURL("modstarter");?>"
		class="btn btn-default"><?php translate("cancel");?></a>
</p>
<p>
	<strong><?php translate("module_folder")?></strong> <br /> <input
		type="text" name="module_folder" maxlength="20"
		value="<?php esc($model->module_folder);?>"
		<?php if($model->edit){echo "readonly";}?> required>
</p>
<p>
	<strong><?php translate("source");?></strong><br /> <select
		name="source">
		<?php foreach($sources as $source){?>
		<option value="<?php esc($source);?>"
			<?php if($source == $model->source) echo "selected";?>><?php esc($source);?></option>
		<?php }?></select>
</p>
<p>
	<strong><?php translate("version")?></strong> <br /> <input type="text"
		name="version" maxlength="20" value="<?php esc($model->version);?>"
		required>
</p>
<p>
	<input type="checkbox" name="embeddable" id="embeddable" value="1"
		<?php if($model->embeddable){ echo "checked";};?>> <label
		for="embeddable"><?php translate("embeddable");?></label>
</p>
<p>
	<input type="checkbox" name="shy" id="shy" value="1"
		<?php if($model->shy){ echo "checked";};?>> <label for="shy"> <?php translate("shy");?></label>
</p>
<p>
	<strong><?php translate("main_class")?></strong><br /> <input
		type="text" name="main_class"
		<?php if($model->edit) { echo "readonly";}?>
		value="<?php esc($model->main_class);?>" required>
</p>
<p>
	<input type="checkbox" name="create_post_install_script"
		id="create_post_install_script"
		<?php if($model->edit and $model->create_post_install_script) { echo "disabled";}?>
		value="1"
		<?php if($model->create_post_install_script){ echo "checked";};?>> <label
		for="create_post_install_script"><?php translate("create_post_install_script");?></label>
</p>
<p>
	<strong><?php translate("hooks");?></strong> <br /> <select
		name="hooks[]" multiple <?php if($model->edit) { echo "disabled";}?>>
<?php

foreach ( $hooks as $hook ) {
	?>
	<option value="<?php esc($hook);?>"
			<?php if(in_array($hook, $model->hooks))?>><?php esc($hook);?></option>
<?php }?>
</select>
</p>
<p>
	<button type="submit" class="btn btn-success"><?php translate("save")?></button>
</p>
</form>
