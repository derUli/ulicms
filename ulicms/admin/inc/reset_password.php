<?php
if (Settings::get ( "disable_password_reset" )) {
	translate ( "function_is_diabled" );
	die ();
}
?>

<?php
$messame = null;
if (isset ( $_POST ["username"] ) and ! empty ( $_POST ["username"] )) {
	$username = $_POST ["username"];
	$user = getUserByName ( $username );
	if ($user) {
		$passwordReset = new PasswordReset ();
		$token = $passwordReset->addToken ( $user ["id"] );
		$passwordReset->sendMail ( $token, $user ["email"], get_ip () );
		$message = get_translation ( "PASSWORD_RESET_SUCCESSFULL" );
	} else {
		$message = get_translation ( "NO_SUCH_USER" );
	}
}
?>
<h1>
<?php translate("reset_password");?>
</h1>
<p>
	<a href="./">[<?php
	
	translate ( "back_to_login" );
	?>]</a>
</p>
<form action="index.php?reset_password" method="post">
<?php
csrf_token_html ();
?>
		<strong><?php
		
		translate ( "username" );
		?>
	</strong> <br /> <input type="text" name="username" value=""> <br /> <br />
	<input type="submit"
		value="<?php
		
		translate ( "reset_password" );
		?>">
		<?php
		
		if ($message) {
			?>
	<p class="ulicms_error">
	<?php
			
			echo htmlspecialchars ( $message );
			?>
	</p>

	<?php
		}
		?>
</form>
