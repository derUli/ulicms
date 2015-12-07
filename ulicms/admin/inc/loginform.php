<?php
require_once ULICMS_ROOT . "/classes/GoogleAuthenticator.php";
$ga = new PHPGangsta_GoogleAuthenticator();
$ga_secret = getconfig("ga_secret");
$qrCodeUrl = $ga->getQRCodeGoogleUrl("UliCMS Login auf ".get_domain(), $ga_secret);

$twofactor_authentication = getconfig("twofactor_authentication");
	
$languages = getAvailableBackendLanguages ();
$default_language = getSystemLanguage ();
if (isset ( $_SESSION ["language"] ) and in_array ( $_SESSION ["language"], $languages )) {
	$default_language = $_SESSION ["language"];
}

$admin_logo = getconfig ( "admin_logo" );
if (! $admin_logo){
	$admin_logo = "gfx/logo.png";
}

   $login_welcome_text = get_lang_config("login_welcome_text", $default_language);
?>
<p>
	<img src="<?php echo $admin_logo;?>" alt="UliCMS" title="UliCMS"
		class="responsive-image" />
</p>
<?php if($login_welcome_text){
?>
<div id="login-welcome-text">
<?php echo nl2br($login_welcome_text);?>
</div>
<?php } ?>
<h3 id="login-please-headline">
<?php

echo TRANSLATION_PLEASE_AUTHENTICATE;
?>
</h3>
<form id="login-form" action="index.php" method="post">
<?php

csrf_token_html ();
?>
	<input type="hidden" name="login" value="login">
	<?php
	
	if (! empty ( $_REQUEST ["go"] )) {
		?>
	<input type="hidden" name="go"
		value='<?php
		echo htmlspecialchars ( $_REQUEST ["go"] )?>'>
	<?php
	}
	?>
	<table>
		<tr>
			<td><strong><strong><?php
			
			echo TRANSLATION_USERNAME;
			?></strong></td>
			<td><input type="text" name="user" value=""></td>
		</tr>
		<tr>
			<td><strong><?php
			
			echo TRANSLATION_PASSWORD;
			?></strong></td>
			<td><input type="password" id="password" name="password" value=""></td>
		</tr>
		<tr>
		<td><label for="view_password"><?php translate("view_password");?></strong></td>
		<td><input type="checkbox" id="view_password"/></td>
		</tr>
		<tr>
			<td><strong><?php
			
			echo TRANSLATION_LANGUAGE;
			?></strong></td>
			<td><select name="system_language">
			<?php
			for($i = 0; $i < count ( $languages ); $i ++) {
				if ($default_language == $languages [$i]) {
					
					echo '<option value="' . $languages [$i] . '" selected>' . getLanguageNameByCode ( $languages [$i] ) . '</option>';
				} else {
					echo '<option value="' . $languages [$i] . '">' . getLanguageNameByCode ( $languages [$i] ) . '</option>';
				}
			}
			?>
			</select></td>
		</tr>
<?php if($twofactor_authentication){
?>
		<tr>
<td>
<strong><?php translate("confirmation_code");?></strong>
</td>
<td><input type="text" name="confirmation_code" value=""></td>
</tr>
<?php }
?>
		<tr>
			<td></td>
			<td style="padding-top: 10px; text-align: center;"><input
				type="submit" value="<?php
				
				echo TRANSLATION_LOGIN;
				?>"></td>
		</tr>
	</table>
</form>
<script type="text/javascript">
$(document).ready(function(){
	bindTogglePassword("#password", "#view_password")
});
</script>
<?php

if (isset ( $_REQUEST ["error"] ) and ! empty ( $_REQUEST ["error"] )) {
	?>
	<script type="text/javascript">
	$(window).load(function(){
	   shake("form#login-form");
	   
	} );
	</script>
<p class="ulicms_error">
<?php
	
	echo htmlspecialchars ( $_REQUEST ["error"] );
	?>
</p>
<?php
}
?>
				<?php
				
				if (getconfig ( "visitors_can_register" ) === "on" or getconfig ( "visitors_can_register" ) === "1") {
					
					?>
<a
	href="?register=register&<?php
					if (! empty ( $_REQUEST ["go"] )) {
						echo "go=" . real_htmlspecialchars ( $_REQUEST ["go"] );
					}
					?>">[<?php
					
					translate ( "register" );
					?>]</a>
<?php
				}
				?>
				<?php
				
				if (! getconfig ( "disable_password_reset" )) {
					?>
<a href="?reset_password" style="float: right;">[<?php
					
					translate ( "reset_password" );
					?>]</a>
<?php
				}
				?>
