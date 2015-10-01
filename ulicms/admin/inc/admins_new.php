<?php
if (defined ( "_SECURITY" )) {
	if (is_admin () or $acl->hasPermission ( "users" )) {
		
		$query = db_query ( "SELECT * FROM " . tbname ( "users" ) . " ORDER BY id", $connection );
		if (db_num_rows ( $query )) {
			?>
<form action="index.php?action=admins" method="post">
<?php
			
			csrf_token_html ();
			?>
	<input type="hidden" name="add_admin" value="add_admin"> <strong><?php
			
			echo TRANSLATION_USERNAME;
			?></strong><br /> <input type="text" required="true"
		name="admin_username" value=""> <br /> <br /> <strong><?php
			
			echo TRANSLATION_LASTNAME;
			?></strong><br /> <input type="text" name="admin_lastname"
		value=""> <br /> <br /> <strong><?php
			
			echo TRANSLATION_FIRSTNAME;
			?></strong><br /> <input type="text" name="admin_firstname"
		value=""><br /> <br /> <strong><?php
			
			echo TRANSLATION_EMAIL;
			?></strong><br /> <input type="email" name="admin_email"
		value=""><br /> <br /> <strong><?php
			
			echo TRANSLATION_PASSWORD;
			?></strong><br /> <input type="text" required="true"
		name="admin_password" value=""><br /> <br /> <input type="checkbox"
		value="1" name="require_password_change" id="require_password_change">
	<label for="require_password_change"><?php
			
			translate ( "REQUIRE_PASSWORD_CHANGE_ON_NEXT_LOGIN" );
			?> </label> <br /> <br /> <input type="checkbox"
		id="send_mail" name="send_mail" value="sendmail"> <label
		for="send_mail"><?php
			
			echo TRANSLATION_SEND_LOGINDATA_BY_MAIL;
			?></label> <br /> <br /> <input type="checkbox" value="1"
		name="admin" id="admin"> <label for="admin"><?php
			
			translate ( "is_admin" );
			?> </label><span style="cursor: help;"
		onclick="$('div#is_admin').slideToggle()">[?]</span><br />
	<div id="is_admin" class="help" style="display: none">
	<?php
			echo nl2br ( get_translation ( "HELP_IS_ADMIN" ) );
			?>
	</div>
	<br /> <input type="checkbox" value="1"
		name="locked" id="locked"> <label for="locked"><?php
			
			translate ( "locked" );
			?> </label>
			
	<br /> 
	<br /> 
	<input type="submit"
		value="<?php
			
			echo TRANSLATION_CREATE_USER;
			?>">
			<?php
			if (getconfig ( "override_shortcuts" ) == "on" || getconfig ( "override_shortcuts" ) == "backend") {
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


	<?php
}
?>