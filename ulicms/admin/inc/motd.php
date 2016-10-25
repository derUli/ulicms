<?php
$acl = new ACL ();
if ($acl->hasPermission ( "motd" )) {
	?>
<div>
	<h2>
	<?php
	
	translate ( "motd" );
	?>
	</h2>
	<?php
	if (isset ( $_POST ["motd"] )) {
		Settings::set ( "motd", $_POST ["motd"] );
		?>
	<p>
	<?php translate("motd_was_changed");?>
	</p>
	<?php
	}
	?>

	<form id="motd_form" action="index.php?action=motd" method="post">
	<?php
	
	csrf_token_html ();
	?>
		<p>
			<textarea name="motd" id="motd" cols=60 rows=15><?php
	echo htmlspecialchars ( Settings::get ( "motd" ) );
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
		<noscript>
			<p style="color: red;">
				Der Editor benötigt JavaScript. Bitte aktivieren Sie JavaScript. <a
					href="http://jumk.de/javascript.html" target="_blank">[Anleitung]</a>
			</p>
		</noscript>
		<input type="submit" name="motd_submit"
			value="<?php translate("save_changes");?>">
		<?php
	if (Settings::get ( "override_shortcuts" ) == "on" || Settings::get ( "override_shortcuts" ) == "backend") {
		?>
		<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php
	}
	?>
	</form>
</div>


<?php
} else {
	noperms ();
}

?>
