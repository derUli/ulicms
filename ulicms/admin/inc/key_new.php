<?php
if (defined ( "_SECURITY" )) {
	$acl = new ACL ();
	if ($acl->hasPermission ( "expert_settings" )) {
		?>

<form action="index.php?action=settings" method="post">
<?php
		
		csrf_token_html ();
		?>
	<input type="hidden" name="add_key" value="add_key"> <strong><?php
		
		echo TRANSLATION_OPTION;
		?>
	</strong><br /> <input type="text" name="name" value=""> <br /> <br />
	<strong><?php
		
		echo TRANSLATION_VALUE;
		?>
	</strong><br /> <textarea name="value" rows=15 cols=80></textarea> <br />
	<br /> <input type="submit"
		value="<?php
		
		echo TRANSLATION_CREATE_OPTION;
		?>">
		<?php
		if (Settings::get ( "override_shortcuts" ) == "on" || Settings::get ( "override_shortcuts" ) == "backend") {
			?>
	<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php
		}
		?>
</form>

<?php
	} else {
		noperms ();
	}
	
	?>




	<?php
}
?>
