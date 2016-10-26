<?php
$acl = new ACL ();

if (defined ( "_SECURITY" )) {
	if ($acl->hasPermission ( "expert_settings" ) and $acl->hasPermission ( "expert_settings_edit" )) {
		$key = intval ( $_GET ["key"] );
		$query = db_query ( "SELECT * FROM " . tbname ( "settings" ) . " WHERE id='$key'" );
		while ( $row = db_fetch_object ( $query ) ) {
			?>

<form action="index.php?action=settings" method="post">
<?php
			
			csrf_token_html ();
			?>
	<input type="hidden" name="id"
		value="<?php
			
			echo $row->id;
			?>"> <input type="hidden" name="edit_key" value="edit_key"> <strong><?php translate ( "option" );?></strong><br />
	<input type="text" name="name"
		value="<?php
			
			echo htmlspecialchars ( $row->name, ENT_QUOTES, "UTF-8" );
			?>"
		readonly="readonly"> <br /> <br /> <strong><?php translate("option");?></strong><br />
	<textarea name="value" rows=15 cols=80><?php
			
			echo htmlspecialchars ( $row->value, ENT_QUOTES, "UTF-8" );
			?></textarea> <br /> <br /> <input type="submit"
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


<?php
			break;
		}
		?>
		<?php
	} else {
		noperms ();
	}
	
	?>




	<?php
}
?>
