<?php
require_once ULICMS_ROOT . "/classes/GoogleAuthenticator.php";
$ga = new PHPGangsta_GoogleAuthenticator();
$secret = $ga->createSecret();
$_SESSION["ga_secret"] = $secret;
$qrCodeUrl = $ga->getQRCodeGoogleUrl(sha1(get_domain()), $secret);


$languages = getAvailableBackendLanguages ();
$default_language = getSystemLanguage ();
if (isset ( $_SESSION ["language"] ) and in_array ( $_SESSION ["language"], $languages )) {
	$default_language = $_SESSION ["language"];
}

$admin_logo = getconfig ( "admin_logo" );
if (! $admin_logo)
	$admin_logo = "gfx/logo.png";
?>
<p>
	<img src="<?php echo $admin_logo;?>" alt="UliCMS" title="UliCMS"
		class="responsive-image" />
</p>
<h3>
<?php

echo TRANSLATION_PLEASE_AUTHENTICATE;
?>
</h3>
<form action="index.php" method="post">
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
	<table style="border: 0px;">
		<tr>
			<td><strong><strong><?php
			
			echo TRANSLATION_USERNAME;
			?></strong></td>
			<td><input type="text" name="user" value=""></td>
		</tr>
		<tr>
			<td><strong><strong><?php
			
			echo TRANSLATION_PASSWORD;
			?></strong></td>
			<td><input type="password" name="password" value=""></td>
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

		<tr>
<td>
<strong><?php translate("confirmation_code");?></strong>
</td>
<td><input type="password" name="confirmation_code" value=""></td>
</tr>
<tr>
<td></td>
<td><img src="<?php echo $qrCodeUrl;?>" alt="QR-Code mit Google Authenticator scannen" title="QR-Code mit Google Authenticator scannen"/>
</td>
</tr>
		<tr>
			<td></td>
			<td style="padding-top: 10px; text-align: center;"><input
				type="submit" value="<?php
				
				echo TRANSLATION_LOGIN;
				?>"></td>
		</tr>
	</table>
</form>
<?php

if (isset ( $_REQUEST ["error"] ) and ! empty ( $_REQUEST ["error"] )) {
	?>
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
