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
?>
<h1><?php translate("create_module");?></h1>
<?php echo ModuleHelper::buildMethodCallForm("ModStarter", "save");?>
<p>
	<strong><?php translate("module_folder")?></strong> <br /> <input type="text"
		name="module_folder" maxlength="20" value="" required>
</p>
<p>
	<a href="<?php echo ModuleHelper::buildAdminURL("modstarter");?>"
		class="btn btn-default"><?php translate("cancel");?></a>
</p>

<p>
	<strong><?php translate("source");?></strong><br /> <select
		name="source">
		<?php foreach($sources as $source){?>
		<option value="<?php esc($source);?>"><?php esc($source);?></option>
		<?php }?></select>
</p>
<p>
	<strong><?php translate("version")?></strong> <br /> <input type="text"
		name="version" maxlength="20" value="" required>
</p>
<p>
	<input type="checkbox" name="embeddable" id="embeddable" value=""> <label
		for="embeddable"><?php translate("embeddable");?></label>
</p>
<p>
	<input type="checkbox" name="shy" id="shy" value=""> <label for="shy"><?php translate("shy");?></label>
</p>
<p>
	<strong><?php translate("main_class")?></strong><br /> <input
		type="text" name="main_class" value="" required>
</p>
<p>
	<input type="checkbox" name="create_post_install_script"
		id="create_post_install_script" value=""> <label
		for="create_post_install_script"><?php translate("create_post_install_script");?></label>
</p>
<p><strong><?php translate("hooks");?></strong>
<br/>
<select name="hooks" multiple>
<?php foreach($hooks as $hook){
	?>
	<option value="<?php esc($hook);?>"><?php esc($hook);?></option>
<?php }?>
</select>
</p>
<p>
	<button type="submit" class="btn btn-success"><?php translate("save")?></button>
</p>
</form>
