<?php
$acl = new ACL ();
if ($acl->hasPermission ( "expert_settings" ) and $acl->hasPermission ( "expert_settings_edit" )) {
	$name = "";
	$value = "";
	if (Request::hasVar ( "name" )) {
		$name = Request::getVar ( "name" );
		$value = Settings::get ( $name );
		if (is_null ( $value )) {
			Request::javascriptRedirect ( ModuleHelper::buildActionURL ( "settings" ) );
		}
	}
	?>
<?php echo ModuleHelper::buildMethodCallForm("ExpertSettingsController", "save");?>

<p>
	<a href="<?php echo ModuleHelper::buildActionURL("settings");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<strong><?php translate("option");?></strong>
<br />
<input type="text" name="name" value="<?php Template::escape($name)?>"
	<?php if($name) echo "readonly";?>>
<br />
<br />
<strong><?php translate("value");?>
	</strong>
<br />
<textarea name="value" rows=15 cols=80><?php Template::escape($value);?></textarea>
<br />
<br />
<button type="submit" class="btn btn-primary"><?php translate("create_option");?></button>
</form>

<?php
} else {
	noperms ();
}
