<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("privacy_settings")) {
    $currentLanguage = Request::getVar("language");
    if (! $currentLanguage) {
        $currentLanguage = Settings::get("default_language");
    }
    $privacy_policy_checkbox_enable = $currentLanguage ? Settings::get("privacy_policy_checkbox_enable_{$currentLanguage}") : Settings::get("privacy_policy_checkbox_enable");
    $log_ip = Settings::get("log_ip");
    $delete_ips_after_48_hours = Settings::get("delete_ips_after_48_hours");
    $keep_spam_ips = Settings::get("keep_spam_ips");
    
    $languages = getAllLanguages(true);
    ?>
<div>
	<p>
		<a
			href="<?php echo ModuleHelper::buildActionURL("settings_categories");?>"
			class="btn btn-default btn-back"><?php translate("back")?></a>
	</p>	<?php
    if (Request::getVar("save")) {
        ?>
	<div class="alert alert-success">
	<?php translate("changes_was_saved");?>
	</div>
	<?php }?>
	<h2><?php translate ( "privacy" );?></h2>

	<div id="accordion-container">
		<h2 class="accordion-header"><?php translate ( "dsgvo_checkbox" );?></h2>
		<div class="accordion-content">
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
		</div>
		<h2 class="accordion-header">
		<?php translate("log");?>
		</h2>

		<div class="accordion-content">
			<p>
			<?php
    
    translate("LOG_IP_INFORMATION");
    ?>
			</p>
			<div class="label">
				<label for="log_ip"> <?php
    
    translate("LOG_IP_ADDRESSES");
    ?>
				</label>
			</div>
			<div class="inputWrapper">
				<input type="checkbox" id="log_ip" name="log_ip"
					<?php
    
    if ($log_ip) {
        echo "checked ";
    }
    ?>>
			</div>
			<?php
    
    translate("LOG_IP_ADDRESSES_NOTICE");
    ?>
	<hr />
			<div class="label">
				<label for="delete_ips_after_48_hours">
	<?php translate("DELETE_IPS_AFTER_48_HOURS");?>
				</label>
			</div>
			<div class="inputWrapper">
				<input type="checkbox" id="delete_ips_after_48_hours"
					name="delete_ips_after_48_hours"
					<?php
    
    if ($delete_ips_after_48_hours) {
        echo "checked ";
    }
    ?>>
			</div>
			<div class="label">
				<label for="keep_spam_ips">
	<?php translate("keep_spam_ips");?>
				</label>
			</div>
			<div class="inputWrapper">
				<input type="checkbox" id="keep_spam_ips" name="keep_spam_ips"
					<?php
    
    if ($keep_spam_ips) {
        echo "checked ";
    }
    ?>>
			</div>
		</div>
	</div>
</div>
<p>
	<button type="submit" class="btn btn-primary voffset2"><?php translate("save_changes");?></button>
</p>
<?php
    enqueueScriptFile("scripts/privacy.js");
    combinedScriptHtml();
    ?>
<?php echo ModuleHelper::endForm();?>
</div>


<?php
} else {
    noPerms();
}
