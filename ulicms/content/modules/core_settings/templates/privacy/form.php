<?php
$acl = new ACL();
if ($acl->hasPermission("privacy_settings")) {
    $currentLanguage = Request::getVar("language");
    if (! $currentLanguage) {
        $currentLanguage = Settings::get("default_language");
    }
    $privacy_policy_checkbox_enable = $currentLanguage ? Settings::get("privacy_policy_checkbox_enable_{$currentLanguage}") : Settings::get("privacy_policy_checkbox_enable");
    ?>
<div>
	<p>
		<a
			href="<?php echo ModuleHelper::buildActionURL("settings_categories");?>"
			class="btn btn-default btn-back"><?php translate("back")?></a>
	</p>
	<h2><?php translate ( "privacy" );?>
	</h2>
	<?php
    $languages = getAllLanguages(true);
    if (Request::getVar("save")) {
        ?>
	<div class="alert alert-success">
	<?php translate("changes_was_saved");?>
	</div>
	<?php }?>
	<?php
    echo ModuleHelper::buildMethodCallForm("PrivacyController", "save", array(), "post", array(
        "id" => "privacy_form"
    ));
    ?>
		<p>
		<strong><?php translate("language");?></strong> <br /> <select
			name="language" id="language">
			<?php
    
    foreach ($languages as $language) {
        ?>
		<option value="<?php Template::escape($language);?>"
				<?php if($currentLanguage == $language){ echo "selected";}?>><?php Template::escape(getLanguageNameByCode($language));?></option>
		<?php }?>

		</select>
	</p>
	<?php
    
    csrf_token_html();
    ?>
<p>
		<input type="checkbox" id="privacy_policy_checkbox_enable"
			name="privacy_policy_checkbox_enable" value="1"
			<?php if($privacy_policy_checkbox_enable) echo "checked";?>> <label
			for="privacy_policy_checkbox_enable"><?php translate("privacy_policy_checkbox_enable");?></label>
	</p>
	<?php
    $editor = get_html_editor();
    ?>
		<div id="privacy_policy_checkbox_text_container"
		style="<?php echo $privacy_policy_checkbox_enable ? "display:block" : "display:none";?>">
		<strong><?php translate("privacy_policy_checkbox_text")?></strong><br />
		<textarea name="privacy_policy_checkbox_text"
			class="<?php esc($editor);?>" data-mimetype="text/html"
			id="privacy_policy_checkbox_text" cols=60 rows=15><?php
    echo htmlspecialchars(Settings::get("privacy_policy_checkbox_text_{$currentLanguage}"));
    ?></textarea>
	</div>
	<p>
		<button type="submit" class="btn btn-primary voffset2"><?php translate("save_changes");?></button>
	</p>
	<?php
    enqueueScriptFile("scripts/privacy.js");
    combinedScriptHtml();
    ?>

	</form>
</div>


<?php
} else {
    noperms();
}
