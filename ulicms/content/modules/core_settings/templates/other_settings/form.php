<?php
$ga = new PHPGangsta_GoogleAuthenticator();
$ga_secret = Settings::get("ga_secret");
$qrCodeUrl = $ga->getQRCodeGoogleUrl(get_translation("ULICMS_LOGIN_AT") . " " . get_domain(), $ga_secret);
$acl = new ACL();
if (! $acl->hasPermission("other")) {
    noPerms();
} else {
    $cache_enabled = ! Settings::get("cache_disabled");
    $cache_period = round(Settings::get("cache_period") / 60);
    $email_mode = Settings::get("email_mode");
    $menus = getAllMenus();
    $force_password_change_every_x_days = intval(Settings::get("force_password_change_every_x_days"));
    $max_failed_logins_items = Settings::get("max_failed_logins_items");
    
    $hide_meta_generator = Settings::get("hide_meta_generator");
    
    $smtp_encryption = Settings::get("smtp_encryption");
    
    $smtp_no_verify_certificate = Settings::get("smtp_no_verify_certificate");
    
    $smtp_host = Settings::get("smtp_host");
    if (! $smtp_host) {
        $smtp_host = "127.0.0.1";
    }
    
    $smtp_port = Settings::get("smtp_port");
    if (! $smtp_port) {
        $smtp_port = "25";
    }
    
    $smtp_user = Settings::get("smtp_user");
    if (! $smtp_user) {
        $smtp_user = null;
    }
    $smtp_password = Settings::get("smtp_password");
    if (! $smtp_password) {
        $smtp_password = null;
    }
    
    $smtp_auth = Settings::get("smtp_auth");
    $no_auto_cron = Settings::get("no_auto_cron");
    $twofactor_authentication = Settings::get("twofactor_authentication");
    ?>
<?php
    echo ModuleHelper::buildMethodCallForm("OtherSettingsController", "save", array(), "post", array(
        "id" => "other_settings"
    ));
    ?>
<p>
	<a
		href="<?php echo ModuleHelper::buildActionURL("settings_categories");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<div id="accordion-container">
	<h2 class="accordion-header"><?php translate ( "page_cache" );?></h2>

	<div class="accordion-content">

		<div class="label">
			<label for="cache_enabled"><?php translate("cache_enabled");?>
				</label>
		</div>
		<div class="inputWrapper">
			<input type="checkbox" id="cache_enabled" name="cache_enabled"
				value="cache_enabled"
				<?php
    
    if ($cache_enabled)
        echo " checked=\"checked\"";
    ?>>
		</div>
		<div class="label">
			<?php
    
    translate("CACHE_VALIDATION_DURATION");
    ?>
			</div>
		<div class="inputWrapper">
			<input type="number" name="cache_period" min="1" max="20160"
				value="<?php
    
    echo $cache_period;
    ?>">
	<?php translate("minutes");?>
			</div>
	</div>
	<h2 class="accordion-header">
		<?php translate("DOMAIN2LANGUAGE_MAPPING");?>
		</h2>

	<div class="accordion-content">

		<?php translate("DOMAIN2LANGUAGE_MAPPING_INFO");?>

			<p>
			<textarea name="domain_to_language" rows="10" cols="40"><?php
    
    echo real_htmlspecialchars(Settings::get("domain_to_language"));
    ?></textarea>
		</p>
	</div>

	<h2 class="accordion-header">
		<?php translate("cronjobs");?>
		</h2>

	<div class="accordion-content">
		<div class="label">
			<label for="no_auto_cron"> <?php
    
    translate("NO_AUTO_CRON");
    ?>
				</label>
		</div>
		<div class="inputWrapper">
			<input type="checkbox" id="no_auto_cron" name="no_auto_cron"
				<?php
    
    if ($no_auto_cron) {
        echo "checked ";
    }
    ?>>
		</div>
		<p>
			<small><?php translate("no_auto_cron_help", array("\n", "<br/>"));?></small>
		</p>
	</div>

	<h2 class="accordion-header">
		<?php
    
    translate("security");
    ?>
		</h2>

	<div class="accordion-content">
		<h2><?php translate("passwords");?></h2>
		<div class="label">
			<label for="force_password_change_every_x_days"><?php
    
    translate("FORCE_PASSWORD_CHANGE_EVERY_X_DAYS");
    ?>
				</label>

		</div>

		<div class="inputWrapper">
			<input type="number" name="force_password_change_every_x_days"
				min="0" max="999"
				value="<?php
    
    echo $force_password_change_every_x_days;
    ?>" />
		</div>
		<br />

		<div class="label">
			<label for="max_failed_logins_items"><?php
    
    translate("max_failed_login_items");
    ?>
				</label>

		</div>

		<div class="inputWrapper">
			<input type="number" name="max_failed_logins_items" min="0" max="999"
				value="<?php
    
    echo intval($max_failed_logins_items);
    ?>" />
		</div>

		<h2><?php translate("google_authenticator");?></h2>
		<div class="label">
			<label for="twofactor_authentication"><?php
    
    translate("2_FACTOR_AUTHENTICATION_ENABLED");
    ?>
				</label>
		</div>
		<div class="inputWrapper">
			<input type="checkbox" id="twofactor_authentication"
				name="twofactor_authentication"
				<?php
    
    if ($twofactor_authentication) {
        echo "checked ";
    }
    ?>>
		</div>
		<p>
			<img src="<?php echo $qrCodeUrl;?>"
				alt="QR-Codemit Google Authenticator scannen"
				title="QR-Code mit Google Authenticator scannen" />
		</p>
		<p>
			<a href="https://support.google.com/accounts/answer/1066447"
				target="_blank">[<?php translate("help");?>]</a>
		</p>
			
			<?php
    
    if ($acl->hasPermission("default_access_restrictions_edit")) {
        ?>
				<h2><?php translate("DEFAULT_ACCESS_RESTRICTIONS");?></h2>
		<p>
			[<a
				href="<?php echo ModuleHelper::buildActionURL("default_access_restrictions");?>"><?php translate("view");?></a>]
		</p>
				<?php
    }
    ?>
		</div>

	<h2 class="accordion-header">
		<?php translate("ADDITIONAL_META_TAGS");?>
		</h2>

	<div class="accordion-content">

		<div class="label">
			<label for="show_meta_generator"><?php translate ( "SHOW_META_GENERATOR" );?>
				</label>
		</div>
		<div class="inputWrapper">
			<input type="checkbox" id="show_meta_generator"
				name="show_meta_generator"
				<?php
    
    if (! $hide_meta_generator) {
        echo "checked ";
    }
    ?>>
		</div>
	</div>
	<h2 class="accordion-header">
		<?php translate("EMAIL_DELIVERY");?>
		</h2>


	<div class="accordion-content">
		<div class="label">Modus:</div>
		<div class="inputWrapper">
			<select id='email_mode' name="email_mode" size="1">
				<option value="internal"
					<?php
    if ($email_mode == EmailModes::INTERNAL) {
        echo ' selected="selected"';
    }
    ?>>mail()</option>
				<option value="phpmailer"
					<?php
    if ($email_mode == EmailModes::PHPMAILER) {
        echo ' selected="selected"';
    }
    ?>>SMTP</option>
			</select>
		</div>
		<div class="smtp_settings" id="smtp_settings" style="display: none">
			<h3>
				<?php translate("smtp_settings");?>
				</h3>
			<div class="label">
				<?php translate("hostname");?>
				</div>
			<div class="inputWrapper">
				<input type="text" name="smtp_host"
					value="<?php echo real_htmlspecialchars ( $smtp_host );?>">

			</div>


			<div class="label">
			<?php
    
    translate("port");
    ?>
			</div>
			<div class="inputWrapper">
				<input type="text" name="smtp_port"
					value="<?php
    
    echo real_htmlspecialchars($smtp_port);
    ?>">

			</div>

			<div class="label">
				<label for="smtp_auth"> <?php translate("smtp_encryption");?>
			</label>
			</div>
			<div class="inputWrapper">
				<select name="smtp_encryption">
					<option value=""
						<?php if(empty($smtp_encryption)) echo "selected"?>><?php translate("unencrypted");?></option>
					<option value="ssl"
						<?php if($smtp_encryption == "ssl") echo "selected"?>>SSL</option>
					<option value="tls"
						<?php if($smtp_encryption == "tls") echo "selected"?>>TLS</option>
				</select>
			</div>
			<div class="label">
				<label for="smtp_no_verify_certificate"> <?php translate("smtp_no_verify_certificate");?>
			</label>
			</div>
			<div class="inputWrapper">
				<p>
					<input type="checkbox" id="smtp_no_verify_certificate"
						name="smtp_no_verify_certificate"
						<?php
    if ($smtp_no_verify_certificate) {
        echo ' checked="checked"';
    }
    ?>
						value="smtp_no_verify_certificate"> <br /> <small><?php translate("smtp_no_verify_certificate_warning");?></small>
				</p>
			</div>

			<div class="label">
				<label for="smtp_auth"> <?php translate("AUTHENTIFACTION_REQUIRED");?>
			</label>
			</div>
			<div class="inputWrapper">
				<input type="checkbox" id="smtp_auth" name="smtp_auth"
					<?php
    if ($smtp_auth) {
        echo ' checked="checked"';
    }
    ?>
					value="auth">
			</div>


			<div id="smtp_auth_div" style="display: none">
				<div class="label">
			<?php translate("user");?>
</div>
				<div class="inputWrapper">
					<input type="text" name="smtp_user"
						value="<?php
    
    echo real_htmlspecialchars($smtp_user);
    ?>">
				</div>

				<div class="label">
		<?php translate("password");?>
		</div>
				<div class="inputWrapper">
					<input type="password" name="smtp_password"
						value="<?php
    
    echo real_htmlspecialchars($smtp_password);
    ?>">

				</div>

			</div>

		</div>
	</div>

	<script type="text/javascript">
<?php
    // FIXME: Extract this code to an external script
    if ($smtp_auth) {
        ?>
$('#smtp_auth_div').show();
<?php
    }
    ?>
$('#smtp_auth').change(function(){
if($('#smtp_auth').prop('checked')){
   $('#smtp_auth_div').slideDown();
} else {
   $('#smtp_auth_div').slideUp();
}
});
</script>
	<script type="text/javascript">
<?php
    
    // FIXME: Extract this code to an external script
    if ($email_mode == EmailModes::PHPMAILER) {
        ?>
$('#smtp_settings').show();
<?php
    }
    ?>
$('#email_mode').change(function(){
if($('#email_mode').val() == "phpmailer"){
   $('#smtp_settings').slideDown();
} else {
   $('#smtp_settings').slideUp();
}
});

</script>
	<h2 class="accordion-header">
	<?php translate("expert_settings");?>
	</h2>

	<div class="accordion-content">
		<p>
			<a href="index.php?action=settings" class="btn btn-danger"><?php translate("view");?></a>
		</p>
	</div>
</div>

<button type="submit" name="submit" class="btn btn-primary voffset3"><?php translate("save_changes");?></button>
</form>
<script type="text/javascript">
$("#other_settings").ajaxForm({beforeSubmit: function(e){
  $("#message").html("");
  $("#loading").show();
  },
  success:function(e){
  $("#loading").hide();
  $("#message").html("<span style=\"color:green;\"><?php translate("CHANGES_WAS_SAVED");?></span>");
  }
});
</script>
<?php
}
?>
