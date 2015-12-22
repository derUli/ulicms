<?php
@include_once "Cache/Lite.php";

require_once ULICMS_ROOT . "/classes/GoogleAuthenticator.php";
$ga = new PHPGangsta_GoogleAuthenticator ();
$ga_secret = Settings::get ( "ga_secret" );
$qrCodeUrl = $ga->getQRCodeGoogleUrl ( get_translation ( "ULICMS_LOGIN_AT" ) . " " . get_domain (), $ga_secret );

include_once ULICMS_ROOT . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "string_functions.php";

$acl = new ACL ();
if (! $acl->hasPermission ( "other" )) {
	noperms ();
} else {
	
	if (isset ( $_POST ["submit"] )) {
		if (isset ( $_POST ["cache_period"] )) {
			setconfig ( "cache_period", intval ( $_POST ["cache_period"] ) * 60 );
		}
		
		if (isset ( $_POST ["cache_type"] )) {
			setconfig ( "cache_type", db_escape ( $_POST ["cache_type"] ) );
		}
		
		if (isset ( $_POST ["email_mode"] ))
			setconfig ( "email_mode", db_escape ( $_POST ["email_mode"] ) );
		
		if (isset ( $_POST ["domain_to_language"] )) {
			$domain_to_language = $_POST ["domain_to_language"];
			$domain_to_language = str_replace ( "\r\n", "\n", $domain_to_language );
			$domain_to_language = trim ( $domain_to_language );
			setconfig ( "domain_to_language", db_escape ( $domain_to_language ) );
		}
		
		if (isset ( $_POST ["override_shortcuts"] ))
			setconfig ( "override_shortcuts", db_escape ( $_POST ["override_shortcuts"] ) );
		
		if (isset ( $_POST ["cache_enabled"] ))
			deleteconfig ( "cache_disabled" );
		else
			setconfig ( "cache_disabled", "disabled" );
		
		if (isset ( $_POST ["smtp_auth"] ))
			setconfig ( "smtp_auth", "auth" );
		else
			deleteconfig ( "smtp_auth" );
		
		if (isset ( $_POST ["show_meta_generator"] )) {
			deleteconfig ( "hide_meta_generator" );
		} else {
			setconfig ( "hide_meta_generator", "hide" );
		}
		
		if (! isset ( $_POST ["twofactor_authentication"] )) {
			deleteconfig ( "twofactor_authentication" );
		} else {
			setconfig ( "twofactor_authentication", "twofactor_authentication" );
		}
		
		if (! isset ( $_POST ["log_ip"] )) {
			deleteconfig ( "log_ip" );
		} else {
			setconfig ( "log_ip", "log_ip" );
		}
		
		if (! isset ( $_POST ["delete_ips_after_48_hours"] )) {
			deleteconfig ( "delete_ips_after_48_hours" );
		} else {
			setconfig ( "delete_ips_after_48_hours", "delete_ips_after_48_hours" );
		}
		
		if (! isset ( $_POST ["no_auto_cron"] )) {
			deleteconfig ( "no_auto_cron" );
		} else {
			setconfig ( "no_auto_cron", "no_auto_cron" );
		}
		
		if (isset ( $_POST ["smtp_host"] ))
			setconfig ( "smtp_host", db_escape ( $_POST ["smtp_host"] ) );
		
		if (isset ( $_POST ["smtp_port"] ))
			setconfig ( "smtp_port", intval ( $_POST ["smtp_port"] ) );
		
		if (isset ( $_POST ["force_password_change_every_x_days"] ))
			setconfig ( "force_password_change_every_x_days", intval ( $_POST ["force_password_change_every_x_days"] ) );
		
		if (isset ( $_POST ["max_failed_logins_items"] )) {
			setconfig ( "max_failed_logins_items", intval ( $_POST ["max_failed_logins_items"] ) );
		}
		
		if (isset ( $_POST ["smtp_user"] ))
			setconfig ( "smtp_user", db_escape ( $_POST ["smtp_user"] ) );
		
		if (isset ( $_POST ["smtp_password"] ))
			setconfig ( "smtp_password", db_escape ( $_POST ["smtp_password"] ) );
		
		if ($_POST ["move_from"] != "-" and $_POST ["move_to"] != "-") {
			db_query ( "UPDATE " . tbname ( "content" ) . " SET menu='" . db_escape ( $_POST ["move_to"] ) . "' WHERE menu='" . db_escape ( $_POST ["move_from"] ) . "'" );
		}
	}
	
	$cache_type = Settings::get ( "cache_type" );
	$cache_enabled = ! Settings::get ( "cache_disabled" );
	$cache_period = round ( Settings::get ( "cache_period" ) / 60 );
	$override_shortcuts = Settings::get ( "override_shortcuts" );
	$email_mode = Settings::get ( "email_mode" );
	$menus = getAllMenus ();
	$force_password_change_every_x_days = intval ( Settings::get ( "force_password_change_every_x_days" ) );
	$max_failed_logins_items = Settings::get ( "max_failed_logins_items" );
	
	$hide_meta_generator = Settings::get ( "hide_meta_generator" );
	
	$smtp_host = Settings::get ( "smtp_host" );
	if (! $smtp_host)
		$smtp_host = "127.0.0.1";
	
	$smtp_port = Settings::get ( "smtp_port" );
	if (! $smtp_port)
		$smtp_port = "25";
	
	$smtp_user = Settings::get ( "smtp_user" );
	if (! $smtp_user)
		$smtp_user = null;
	
	$smtp_password = Settings::get ( "smtp_password" );
	if (! $smtp_password)
		$smtp_password = null;
	
	$smtp_auth = Settings::get ( "smtp_auth" );
	$log_ip = Settings::get ( "log_ip" );
	$delete_ips_after_48_hours = Settings::get ( "delete_ips_after_48_hours" );
	$no_auto_cron = Settings::get ( "no_auto_cron" );
	$twofactor_authentication = Settings::get ( "twofactor_authentication" );
	
	?>


<form id="other_settings" action="index.php?action=other_settings"
	method="post">
	<?php
	
	csrf_token_html ();
	?>
	<div id="accordion-container">
		<h2 class="accordion-header">
		<?php
	
	translate ( "page_cache" );
	?>
		</h2>

		<div class="accordion-content">

			<div class="label">
				<label for="cache_enabled"><?php
	
	echo TRANSLATION_CACHE_ENABLED;
	?>
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
	
	echo TRANSLATION_CACHE_VALIDATION_DURATION;
	?>
			</div>
			<div class="inputWrapper">
				<input type="number" name="cache_period" min=1 max=20160
					value="<?php
	
	echo $cache_period;
	?>">
	<?php
	
	echo TRANSLATION_MINUTES;
	?>
			</div>

			<div class="label">
			<?php
	
	echo TRANSLATION_CACHE_ENGINE;
	?>
			</div>
			<div class="inputWrapper">
				<select name="cache_type" size=1>
					<option value="file"
						<?php
	
	if ($cache_type === "file" or ! $cache_type) {
		echo " selected";
	}
	?>>
					<?php
	
	echo TRANSLATION_FILE;
	?></option>
					<option value="cache_lite"
						<?php
	
	if ($cache_type === "cache_lite") {
		echo " selected";
	}
	?>>
						Cache_Lite
						<?php if(!class_exists("Cache_Lite")) echo " (nicht verfügbar)"?>
					</option>
				</select>
			</div>

		</div>


		<h2 class="accordion-header">
		<?php
	
	echo TRANSLATION_SHORTCUTS;
	?>
		</h2>

		<div class="accordion-content">
			<div class="label">
			<?php
	
	echo TRANSLATION_REPLACE_SHORTCUTS;
	?>
			</div>
			<div class="inputWrapper">
				<select name="override_shortcuts" size=1>
					<option value="off"
						<?php if($override_shortcuts == "off" or !$override_shortcuts) echo " selected=\"selected\""?>>
						<?php
	
	echo TRANSLATION_OFF;
	?>
					</option>
					<option value="frontend"
						<?php if($override_shortcuts == "frontend") echo " selected=\"selected\""?>>
						<?php
	
	echo TRANSLATION_ONLY_IN_FRONTEND;
	?>
					</option>
					<option value="backend"
						<?php if($override_shortcuts == "backend") echo " selected=\"selected\""?>>
						<?php
	
	echo TRANSLATION_ONLY_IN_BACKEND;
	?>
					</option>
					<option value="on"
						<?php if($override_shortcuts == "on") echo " selected=\"selected\""?>>
						<?php
	
	echo TRANSLATION_BOOTH_BACKEND_AND_FRONTEND;
	?>
					</option>
				</select>
			</div>
			<p>
			<?php
	
	echo TRANSLATION_REPLACE_SHORTCUTS_INFO;
	?>
			</p>
		</div>





		<h2 class="accordion-header">
		<?php
	
	echo TRANSLATION_MOVE_MENU_ITEMS;
	?>
		</h2>


		<div class="accordion-content">
			<p>
			<?php
	
	echo TRANSLATION_MOVE_ALL_MENU_ITEMS_FROM;
	?>
				<select name="move_from" size="1">
					<option value="-" selected>-</option>
					<?php
	
	foreach ( $menus as $menu ) {
		?>
					<option value="<?php echo $menu?>">
					<?php echo $menu?>
					</option>
					<?php
	}
	?>
				</select>
				<?php
	
	echo TRANSLATION_MOVE_ALL_MENU_ITEMS_TO;
	?>
				<select name="move_to" size="1">
					<option value="-" selected>-</option>
					<?php
	
	foreach ( $menus as $menu ) {
		?>
					<option value="<?php echo $menu?>">
					<?php echo $menu?>
					</option>
					<?php
	}
	?>
				</select>
			</p>
		</div>

		<h2 class="accordion-header">
		<?php
	
	echo TRANSLATION_DOMAIN2LANGUAGE_MAPPING;
	?>
		</h2>

		<div class="accordion-content">

		<?php
	
	echo TRANSLATION_DOMAIN2LANGUAGE_MAPPING_INFO;
	?>

			<p>
				<textarea name="domain_to_language" rows="10" cols="40"><?php
	
	echo real_htmlspecialchars ( Settings::get ( "domain_to_language" ) );
	?></textarea>
			</p>
		</div>
		<h2 class="accordion-header">
		<?php
	
	echo TRANSLATION_LOG;
	?>
		</h2>

		<div class="accordion-content">
			<p>
			<?php
	
	translate ( "LOG_IP_INFORMATION" );
	?>
			</p>
			<div class="label">
				<label for="log_ip"> <?php
	
	translate ( "LOG_IP_ADDRESSES" );
	?>
				</label>
			</div>
			<div class="inputWrapper">
				<input type="checkbox" id="log_ip" name="log_ip"
					<?php
	
	if ($log_ip) {
		echo "checked ";
	}
	?>>
			</div>
			<?php
	
	translate ( "LOG_IP_ADDRESSES_NOTICE" );
	?>
	<hr />
			<div class="label">
				<label for="delete_ips_after_48_hours">
	<?php translate("DELETE_IPS_AFTER_48_HOURS");?>
				</label>
			</div>
			<div class="inputWrapper">
				<input type="checkbox" id="delete_ips_after_48_hours"
					name="delete_ips_after_48_hours"
					<?php
	
	if ($delete_ips_after_48_hours) {
		echo "checked ";
	}
	?>>
			</div>
		</div>


		<h2 class="accordion-header">
		<?php
	
	echo TRANSLATION_CRONJOBS;
	?>
		</h2>

		<div class="accordion-content">
			<div class="label">
				<label for="no_auto_cron"> <?php
	
	translate ( "NO_AUTO_CRON" );
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
		</div>
		<h2 class="accordion-header">
		<?php
	
	translate ( "security" );
	?>
		</h2>

		<div class="accordion-content">
			<h2><?php translate("passwords");?></h2>
			<div class="label">
				<label for="force_password_change_every_x_days"><?php
	
	translate ( "FORCE_PASSWORD_CHANGE_EVERY_X_DAYS" );
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
	
	translate ( "max_failed_login_items" );
	?>
				</label>

			</div>

			<div class="inputWrapper">
				<input type="number" name="max_failed_logins_items" min="0"
					max="999"
					value="<?php
	
	echo intval ( $max_failed_logins_items );
	?>" />
			</div>

			<h2><?php translate("google_authenticator");?></h2>
			<div class="label">
				<label for="twofactor_authentication"><?php
	
	translate ( "2_FACTOR_AUTHENTICATION_ENABLED" );
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
		</div>

		<h2 class="accordion-header">
		<?php
	
	echo TRANSLATION_ADDITIONAL_META_TAGS;
	?>
		</h2>

		<div class="accordion-content">

			<div class="label">
				<label for="show_meta_generator"><?php
	
	echo TRANSLATION_SHOW_META_GENERATOR;
	?>
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
		<?php
	
	echo TRANSLATION_EMAIL_DELIVERY;
	?>
		</h2>


		<div class="accordion-content">
			<div class="label">Modus:</div>
			<div class="inputWrapper">
				<select id='email_mode' name="email_mode" size="1">
					<option value="internal"
						<?php
	if ($email_mode == "internal")
		echo ' selected="selected"';
	?>>PHP</option>
					<?php
	
	if (! defined ( "NO_PEAR_MAIL" )) {
		?>
					<option value="pear_mail"
						<?php
		if ($email_mode == "pear_mail")
			echo ' selected="selected"';
		?>>PEAR Mail</option>
					<?php
	}
	?>
				</select>
			</div>
			<div class="smtp_settings" id="smtp_settings" style="display: none">
				<h3>
				<?php
	
	echo TRANSLATION_SMTP_SETTINGS;
	?>
				</h3>
				<div class="label">
				<?php
	
	echo TRANSLATION_HOSTNAME;
	?>
				</div>
				<div class="inputWrapper">
					<input type="text" name="smtp_host"
						value="<?php
	
	echo real_htmlspecialchars ( $smtp_host );
	?>">

				</div>


				<div class="label">
			<?php
	
	echo TRANSLATION_PORT;
	?>
			</div>
				<div class="inputWrapper">
					<input type="text" name="smtp_port"
						value="<?php
	
	echo real_htmlspecialchars ( $smtp_port );
	?>">

				</div>

				<div class="label">
					<label for="smtp_auth"> <?php
	
	echo TRANSLATION_AUTHENTIFACTION_REQUIRED;
	?>
			</label>
				</div>
				<div class="inputWrapper">
					<input type="checkbox" id="smtp_auth" name="smtp_auth"
						<?php
	if ($smtp_auth)
		echo ' checked="checked"'?>
						value="auth">
				</div>


				<div id="smtp_auth_div" style="display: none">
					<div class="label">
			<?php
	
	echo TRANSLATION_USER;
	?>
			</div>
					<div class="inputWrapper">
						<input type="text" name="smtp_user"
							value="<?php
	
	echo real_htmlspecialchars ( $smtp_user );
	?>">
					</div>

					<div class="label">
		<?php
	
	echo TRANSLATION_PASSWORD;
	?>
		</div>
					<div class="inputWrapper">
						<input type="password" name="smtp_password"
							value="<?php
	
	echo real_htmlspecialchars ( $smtp_password );
	?>">

					</div>

				</div>

			</div>
		</div>
		<script type="text/javascript">
<?php
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
	if ($email_mode == "pear_mail") {
		?>
$('#smtp_settings').show();
<?php
	}
	?>

$('#email_mode').change(function(){
if($('#email_mode').val() == "pear_mail"){
   $('#smtp_settings').slideDown();

} else {
   $('#smtp_settings').slideUp();

}

});

</script>



		<h2 class="accordion-header">
	<?php
	
	echo TRANSLATION_EXPERT_SETTINGS;
	?>
	</h2>

		<div class="accordion-content">
			<p>
				<a href="index.php?action=settings"><?php
	
	echo TRANSLATION_VIEW;
	?>
			</a>
			</p>
		</div>
	</div>
	<br /> <input name="submit" type="submit"
		value="<?php
	
	echo TRANSLATION_SAVE_CHANGES;
	?>" />

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
<script type="text/javascript">
$("#other_settings").ajaxForm({beforeSubmit: function(e){
  $("#message").html("");
  $("#loading").show();
  },
  success:function(e){
  $("#loading").hide();
  $("#message").html("<span style=\"color:green;\"><?php
	
	echo TRANSLATION_CHANGES_WAS_SAVED;
	?></span>");
  }


});

</script>
<?php

}
?>
