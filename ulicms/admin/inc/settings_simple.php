<?php
if (defined ( "_SECURITY" )) {
	$acl = new ACL ();
	if ($acl->hasPermission ( "settings_simple" )) {
		
		$query = db_query ( "SELECT * FROM " . tbname ( "settings" ) . " ORDER BY name", $connection );
		$settings = Array ();
		while ( $row = db_fetch_object ( $query ) ) {
			$settings [$row->name] = $row->value;
			$settings [$row->name] = htmlspecialchars ( $settings [$row->name], ENT_QUOTES, "UTF-8" );
		}
		
		?>

<h2><?php translate("general_settings");?></h2>
<p>Hier können Sie die Einstellungen für Ihre Internetseite verändern.</p>
<form id="settings_simple" action="index.php?action=save_settings"
	method="post">
<?php
		
		csrf_token_html ();
		?>
<table>
		<tr>
			<td><strong><?php translate("homepage_title");?></strong></td>
			<td><a href="index.php?action=homepage_title"><?php translate("edit");?></a></td>
		</tr>
		<tr>
			<td><strong><?php translate("motto");?></strong></td>
			<td><a href="index.php?action=motto"><?php translate("edit");?></a></td>
		</tr>
		<tr>
			<td><strong><?php translate("homepage_owner");?></strong></td>
			<td><input type="text" name="homepage_owner"
				value="<?php
		
		echo $settings ["homepage_owner"];
		?>"></td>
		</tr>
		<tr>
			<td><strong><?php translate("hide_logo")?></strong></td>
			<td><select name="logo_disabled" size=1>
					<option
						<?php
		if (Settings::get ( "logo_disabled" ) == "yes") {
			echo 'selected ';
		}
		?>
						value="yes"><?php translate("yes");?></option>
					<option
						<?php
		if (Settings::get ( "logo_disabled" ) != "yes") {
			echo 'selected ';
		}
		?>
						value="no"><?php translate("no");?></option>
			</select></td>
		</tr>
		<tr>
			<td><strong><?php translate("OWNER_MAILADRESS");?></strong></td>
			<td><input type="email" name="email"
				value="<?php
		
		echo $settings ["email"];
		?>"></td>
		</tr>
		<tr>
			<td><strong><?php translate("frontpage");?></strong></td>
			<td><a href="index.php?action=frontpage_settings"><?php
		
		translate ( "edit" );
		?></a></td>
		</tr>
		<tr>
			<td><strong><?php translate("MAINTENANCE_MODE_ENABLED");?></strong></td>
			<td><input type="checkbox" name="maintenance_mode"
				<?php
		if (strtolower ( $settings ["maintenance_mode"] == "on" ) || $settings ["maintenance_mode"] == "1" || strtolower ( $settings ["maintenance_mode"] ) == "true") {
			echo " checked";
		}
		
		?>></td>
		</tr>
		<tr>
			<td><strong><?php translate("GUEST_MAY_REGISTER");?></strong></td>
			<td><input type="checkbox" name="visitors_can_register"
				<?php
		if (strtolower ( $settings ["visitors_can_register"] == "on" ) || $settings ["visitors_can_register"] == "1" || strtolower ( $settings ["visitors_can_register"] ) == "true") {
			echo " checked";
		}
		
		?>></td>
		</tr>
		<tr>
			<td><strong><?php
		
		translate ( "enable_password_reset" );
		?></strong>
			
			<td><input type="checkbox" name="disable_password_reset"
				value="enable"
				<?php if(!isset($settings["disable_password_reset"])) echo " checked"?>>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><strong><?php translate("metadata");?></strong></strong></td>
		</tr>
		<tr>
			<td><strong><?php
		
		translate ( "description" );
		?></strong></td>
			<td><a href="index.php?action=meta_description"><?php
		
		translate ( "edit" );
		?></a></td>
		</tr>
		<tr>
			<td><strong><?php translate("keywords");?></strong></td>
			<td><a href="index.php?action=meta_keywords"><?php
		
		translate ( "edit" );
		?></a></td>
		</tr>
		<?php
		
		if ($acl->hasPermission ( "open_graph" )) {
			?>
              <tr>
			<td><strong><?php translate("open_graph");?>
		</strong></td>
			<td><a href="index.php?action=open_graph"><?php
			
			translate ( "edit" );
			?></a></td>
		</tr>
		   <?php
		}
		?>
		<tr>
			<td></td>
			<td><strong><?php translate("technical_stuff");?></strong></td>
		</tr>
		<tr>
			<td><strong><?php translate("FIX_W3C_VALIDATION_ERRORS");?></strong></td>
			<td><input type="checkbox" name="disable_html_validation"
				<?php
		if (! isset ( $settings ["disable_html_validation"] )) {
			echo " checked";
		}
		?>
				value="enabled">
		
		</tr>

		<tr>
			<td><strong><?php translate ( "timezone" );?></strong></td>
			<td><select name="timezone" size=1>
<?php
		$timezones = file ( "inc/timezones.txt" );
		
		$current_timezone = Settings::get ( "timezone" );
		$current_timezone = trim ( $current_timezone );
		sort ( $timezones );
		for($i = 0; $i < count ( $timezones ); $i ++) {
			
			$thisTimezone = $timezones [$i];
			$thisTimezone = trim ( $thisTimezone );
			if ($thisTimezone === $current_timezone) {
				echo '<option value="' . $thisTimezone . '" selected>' . $thisTimezone . '</option>';
			} else {
				echo '<option value="' . $thisTimezone . '">' . $thisTimezone . '</option>';
			}
		}
		?>
</select></td>
		</tr>
		<tr>
			<td><strong><?php translate("search_engines");?></strong></td>
			<td><select name="robots" size=1>
<?php
		if (Settings::get ( "robots" ) == "noindex,nofollow") {
			?>

   <option value="index,follow"><?php translate("EARCH_ENGINES_INDEX");?></option>
					<option value="noindex,nofollow" selected><?php
			
			translate ( "SEARCH_ENGINES_NOINDEX" );
			?></option>

<?php
		} else {
			?>
   <option value="index,follow" selected><?php
			translate ( "SEARCH_ENGINES_INDEX" );
			?></option>
					<option value="noindex,nofollow"><?php
			
			translate ( "SEARCH_ENGINES_NOINDEX" );
			?></option>
<?php
		}
		?>
</select></td>
		</tr>
<?php
		
		add_hook ( "settings_simple" );
		?>
<tr>
			<td>
			
			<td align="center"><input type="submit" value="OK"
				style="width: 100%;"></td>
		</tr>
	</table>
	<input type="hidden" name="save_settings" value="save_settings">

<?php
		if (Settings::get ( "override_shortcuts" ) == "on" || Settings::get ( "override_shortcuts" ) == "backend") {
			?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php
		}
		?>
</form>
<script type="text/javascript">
$("#settings_simple").ajaxForm({beforeSubmit: function(e){
  $("#message").html("");
  $("#loading").show();
  },
  success:function(e){
  $("#loading").hide();
  $("#message").html("<span style=\"color:green;\">Die Einstellungen wurden gespeichert.</span>");
  }


});

</script>



<?php
	} else {
		noperms ();
	}
}
