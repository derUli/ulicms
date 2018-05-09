<?php
$acl = new ACL ();
if ($acl->hasPermission ( "privacy_settings" )) {
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
	$languages = getAllLanguages ( true );
	if (Request::getVar ( "save" )) {
		?>
	<p>
	<?php translate("changes_were_saved");?>
	</p>
	<?php }?>
	<?php
	echo ModuleHelper::buildMethodCallForm ( "PrivacyController", "save", array (), "post", array (
			"id" => "privacy_form"
	) );
	?>
		<p>
		<strong><?php translate("language");?></strong> <br /> <select
			name="language" id="language">
			<option value=""
				<?php if(!Request::getVar ( "language" )){ echo "selected";}?>>[<?php translate("no_language");?>]</option>
	<?php

	foreach ( $languages as $language ) {
		?>
		<option value="<?php Template::escape($language);?>"
				<?php if(Request::getVar ( "language" ) == $language){ echo "selected";}?>><?php Template::escape(getLanguageNameByCode($language));?></option>
		<?php }?>

		</select>
	</p>
	<?php

	csrf_token_html ();
	?>	<?php
	$editor = get_html_editor ();
	?>
		<p>
		<strong><?php translate("privacy_checkbox_text")?></strong><br/>
		<textarea name="privacy_checkbox_text" class="<?php esc($editor);?>" data-mimetype="text/html" id="privacy_checkbox_text" cols=60 rows=15><?php
	echo htmlspecialchars ( Request::getVar ( "language" ) ? Settings::get ( "privacy_checkbox_text_" . Request::getVar ( "language" ) ) : Settings::get ( "privacy_checkbox_text" ) );
	?></textarea>
	</p>

	<button type="submit" class="btn btn-primary voffset2"><?php translate("save_changes");?></button>
	<?php
	enqueueScriptFile("scripts/privacy.js");
	combinedScriptHtml();
	?>

	</form>
</div>


<?php
} else {
	noperms ();
}
