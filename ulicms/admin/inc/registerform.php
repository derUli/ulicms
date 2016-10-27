<?php
if (Settings::get ( "visitors_can_register" ) == "off" or ! Settings::get ( "visitors_can_register" )){
	die ( get_translation ( "FUNCTION_IS_DISABLED" ) );
}

$errors = false;
if (isset ( $_POST ["register_user"] )) {
	if (empty ( $_POST ["admin_username"] ) or empty ( $_POST ["admin_password"] ) or empty ( $_POST ["admin_firstname"] ) or empty ( $_POST ["admin_lastname"] )) {
		echo "<p style='color:red;'>" . get_translation ( "FILL_ALL_FIELDS" ) . "</p>";
	} else if (user_exists ( $_POST ["admin_username"] )) {
		echo "<p style='color:red;'>" . get_translation ( "USERNAME_ALREADY_EXISTS" ) . "</p>";
	} else if ($_POST ["admin_password"] != $_POST ["admin_password_repeat"]) {
		echo "<p style='color:red;'>" . get_translation ( "PASSWORD_REPEAT_IS_WRONG" ) . "</p>";
	} 
	else {
		$registered_user_default_level = Settings::get ( "registered_user_default_level" );
		if ($registered_user_default_level === false) {
			$registered_user_default_level = 10;
		}
		adduser ( $_POST ["admin_username"], $_POST ["admin_lastname"], $_POST ["admin_firstname"], $_POST ["admin_email"], $_POST ["admin_password"], $registered_user_default_level, false );
		echo "<p style='color:green;'>Registrierung erfolgreich!</p>";
		if (! empty ( $_REQUEST ["go"] )) {
			$go = htmlspecialchars ( $_REQUEST ["go"] );
		} else {
			$go = "index.php";
		}
		echo "<p><a href='$go'>" . get_translation ( "continue_here" ) . "</a></p>";
	}
}
?>
<?php

add_hook ( "before_register_form_title" );
?>
<h1>
<?php translate("registration");?>
</h1>
<p>
	<a href="./">[<?php
	
	translate ( "back_to_login" );
	?>]</a>
</p>
<?php

add_hook ( "before_register_form" );
?>
<form action="index.php?register=register" method="post">
<?php

csrf_token_html ();
?>
	<input type="hidden" name="register_user" value="add_admin">
	<?php
	
	if (! empty ( $_REQUEST ["go"] )) {
		?>
	<input type="hidden" name="go"
		value='<?php
		echo htmlspecialchars ( $_REQUEST ["go"] )?>'>
	<?php
	}
	?>
	<strong><?php translate("username");?>
	</strong><br /> <input type="text" required="required"
		name="admin_username" value=""> <br /> <br /> <strong><?php translate("lastname")?>
	</strong><br /> <input type="text" required="required"
		name="admin_lastname" value=""> <br /> <br /> <strong><?php translate("firstname");?>
	</strong><br /> <input type="text" required="required"
		name="admin_firstname" value=""><br /> <br /> <strong><?php translate("email");?>
	</strong><br /> <input type="email" required="required"
		name="admin_email" value=""><br /> <br /> <strong><?php translate("password");?>
	</strong><br /> <input type="password" required="required"
		name="admin_password" value=""><br /> <br /> <strong><?php translate("password_repeat");?>
	</strong><br /> <input type="password" required="required"
		name="admin_password_repeat" value=""><br /> <br />
		<?php
		
		add_hook ( "register_form_field" );
		?>
	<br />

	<?php
	if (Settings::get ( "override_shortcuts" ) == "on" || Settings::get ( "override_shortcuts" ) == "backend") {
		?>
	<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php
	}
	?>
	<input type="submit" value="<?php translate("register");?>">
</form>

<?php

add_hook ( "after_register_form" );
?>
