<?php
$acl = new ACL ();
if ($acl->hasPermission ( "motd" )) {
	?>
<div>
	<h2><?php translate ( "motd" );?>
	</h2>
	<?php
	$languages = getAllLanguages ( true );
	if (Request::getVar ( "save" )) {
		?>
	<p>
	<?php translate("motd_was_changed");?>
	</p>
	<?php }?>
	<?php
	echo ModuleHelper::buildMethodCallForm ( "MOTDController", "save", array (), "post", array (
			"id" => "motd_form" 
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
		<textarea name="motd" id="motd" cols=60 rows=15><?php
	echo htmlspecialchars ( Request::getVar ( "language" ) ? Settings::get ( "motd_" . Request::getVar ( "language" ) ) : Settings::get ( "motd" ) );
	?></textarea>
	</p>
		<?php
	$editor = get_html_editor ();
	?>

		<?php
	if ($editor === "ckeditor") {
		?>
		<script type="text/javascript">
var editor = CKEDITOR.replace( 'motd',
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
var myCodeMirror = CodeMirror.fromTextArea(document.getElementById("motd"),

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
	<button type="submit" name="motd_submit"
		class="btn btn-success voffset2"><?php translate("save_changes");?></button>
	<script type="text/javascript" src="scripts/motd.js"></script>

	</form>
</div>


<?php
} else {
	noperms ();
}

