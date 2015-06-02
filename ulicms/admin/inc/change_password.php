<?php
include_once ULICMS_ROOT . "/users_api.php";
if (! defined ( "ULICMS_ROOT" )) {
	die ( "direct access not allowed." );
}

if (isset ( $_POST ["change_password"] )) {
	if (! empty ( $_POST ["password"] ) and $_POST ["password"] === $_POST ["password_repeat"]) {
		$uid = get_user_id ();
		changePassword ( $_POST ["password"], $uid );
		db_query ( "UPDATE " . tbname ( "users" ) . " SET `require_password_change` = 0 where id = $uid" );
		$_SESSION ["require_password_change"] = 0;
	} else {
		echo '<p class="ulicms_error">' . get_translation ( "passwords_not_equal" ) . '</p>';
	}
}

if (! $_SESSION ["require_password_change"]) {
	echo '<script type="text/javascript">location.replace(window.location.href);</script>';
} else {
	?>
<form id="change_password_form" action="index.php" method="post">
<?php
	
	csrf_token_html ();
	?>
	<h1>
	<?php
	
	translate ( "change_password" );
	?>
	</h1>
	<p>
	<?php
	
	translate ( "require_password_change_notice" );
	?>
	</p>
	<strong><?php
	
	translate ( "password" );
	?> </strong> <input name="password" id="password" type="password"> <br />
	<br /> <strong><?php
	
	translate ( "password_repeat" );
	?> </strong> <input name="password_repeat" id="password_repeat"
		type="password"> <br /></br> <input type="submit"
		value="<?php
	
	translate ( "save_changes" );
	?>"
		name="change_password">
</form>

<br />
<br />
<?php
}
