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
	?>
		<p>
		<textarea name="privacy_checkbox_text" id="privacy_checkbox_text" cols=60 rows=15><?php
	echo htmlspecialchars ( Request::getVar ( "language" ) ? Settings::get ( "privacy_checkbox_text_" . Request::getVar ( "language" ) ) : Settings::get ( "privacy_checkbox_text" ) );
	?></textarea>
	</p>
		<?php
	$editor = get_html_editor ();
	?>

		<?php
	if ($editor === "ckeditor") {
		?>
		<script type="text/javascript">
var editor = CKEDITOR.replace( 'privacy_checkbox_text',
					{
						skin : '<?php

		echo Settings::get ( "ckeditor_skin" );
		?>'
					});
</script>
<?php
	} else if ($editor == "codemirror") {
		?>
		<script type="text/javascript">
var myCodeMirror = CodeMirror.fromTextArea(document.getElementById("privacy_checkbox_text"),

{lineNumbers: true,
        matchBrackets: true,
        mode : "text/html",

        indentUnit: 0,
        indentWithTabs: false,
        enterMode: "keep",
        tabMode: "shift"});
</script>
<?php
	}
	?>
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
