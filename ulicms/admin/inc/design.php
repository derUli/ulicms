<?php
$acl = new ACL ();
if (! $acl->hasPermission ( "design" )) {
	noperms ();
} else {
	$theme = Settings::get ( "theme" );
	$additional_menus = Settings::get ( "additional_menus" );
	$mobile_theme = Settings::get ( "mobile_theme" );
	
	if (isset ( $_REQUEST ["submit"] )) {
		
		if (! isset ( $_REQUEST ["disable_custom_layout_options"] )) {
			setconfig ( "disable_custom_layout_options", "disable" );
		} else {
			Settings::delete ( "disable_custom_layout_options" );
		}
		
		if (isset ( $_REQUEST ["no_mobile_design_on_tablet"] )) {
			setconfig ( "no_mobile_design_on_tablet", "no_mobile_design_on_tablet" );
		} else {
			Settings::delete ( "no_mobile_design_on_tablet" );
		}
		
		if (isset ( $_REQUEST ["video_width_100_percent"] ))
			setconfig ( "video_width_100_percent", "width" );
		else
			Settings::delete ( "video_width_100_percent" );
		
		if ($_REQUEST ["additional_menus"] !== $additional_menus) {
			setconfig ( "additional_menus", db_escape ( $_REQUEST ["additional_menus"] ) );
		}
		
		// Wenn Formular abgesendet wurde, Wert Speichern
		if ($_REQUEST ["theme"] !== $theme) { // if theme auf
			$themes = getThemesList ();
			if (in_array ( $_REQUEST ["theme"], $themes )) { // if in_array theme auf
				setconfig ( "theme", db_escape ( $_REQUEST ["theme"] ) );
				$theme = $_REQUEST ["theme"];
			} // if in_array theme zu
		} // if theme zu
		  
		// Wenn Formular abgesendet wurde, Wert Speichern
		if ($_REQUEST ["mobile_theme"] !== $mobile_theme) { // if mobile_theme auf
			$themes = getThemesList ();
			
			if (empty ( $_REQUEST ["mobile_theme"] ))
				Settings::delete ( "mobile_theme" );
			else if (in_array ( $_REQUEST ["mobile_theme"], $themes )) { // if in_array mobile_theme auf
				setconfig ( "mobile_theme", db_escape ( $_REQUEST ["mobile_theme"] ) );
				$mobile_theme = $_REQUEST ["mobile_theme"];
			} // if in_array mobile_theme zu
		} // if mobile_theme zu
		
		if ($_REQUEST ["default-font"] != Settings::get ( "default-font" )) {
			if (! empty ( $_REQUEST ["custom-font"] ))
				$font = $_REQUEST ["custom-font"];
			else
				$font = $_REQUEST ["default-font"];
			
			$font = db_escape ( $font );
			
			setconfig ( "default-font", $font );
		}
		
		if (! empty ( $_REQUEST ["google-font"] )) {
			
			$font = $_REQUEST ["google-font"];
			
			$font = db_escape ( $font );
			
			setconfig ( "google-font", $font );
		}
		
		setconfig ( "zoom", intval ( $_REQUEST ["zoom"] ) );
		
		setconfig ( "font-size", db_escape ( $_REQUEST ["font-size"] ) );
		
		setconfig ( "ckeditor_skin", db_escape ( $_REQUEST ["ckeditor_skin"] ) );
		
		if (Settings::get ( "header-background-color" ) != $_REQUEST ["header-background-color"]) {
			setconfig ( "header-background-color", db_escape ( $_REQUEST ["header-background-color"] ) );
		}
		
		if (Settings::get ( "body-text-color" ) != $_REQUEST ["body-text-color"]) {
			setconfig ( "body-text-color", db_escape ( $_REQUEST ["body-text-color"] ) );
		}
		
		if (Settings::get ( "title_format" ) != $_REQUEST ["title_format"])
			setconfig ( "title_format", db_escape ( $_REQUEST ["title_format"] ) );
		
		if (Settings::get ( "body-background-color" ) != $_REQUEST ["body-background-color"]) {
			setconfig ( "body-background-color", db_escape ( $_REQUEST ["body-background-color"] ) );
		}
	} // if submit zu
	
	$allThemes = getThemesList ();
	include_once "inc/fonts.php";
	$fonts = getFontFamilys ();
	$google_fonts = get_google_fonts ();
	$theme = Settings::get ( "theme" );
	$additional_menus = Settings::get ( "additional_menus" );
	$mobile_theme = Settings::get ( "mobile_theme" );
	$default_font = Settings::get ( "default-font" );
	$google_font = Settings::get ( "google-font" );
	$title_format = htmlspecialchars ( Settings::get ( "title_format" ), ENT_QUOTES, "UTF-8" );
	$zoom = intval ( Settings::get ( "zoom" ) );
	$font_size = Settings::get ( "font-size" );
	$ckeditor_skin = Settings::get ( "ckeditor_skin" );
	$video_width_100_percent = Settings::get ( "video_width_100_percent" );
	$font_sizes = getFontSizes ();
	
	$no_mobile_design_on_tablet = Settings::get ( "no_mobile_design_on_tablet" );
	
	?>
	<?php
	
	if ($default_font != "google") {
		?>

<style type="text/css">
div#google-fonts {
	display: none;
}
</style>
<?php
	}
	?>
<h1>
<?php
	
	echo TRANSLATION_DESIGN;
	?>
</h1>
<form id="designForm" action="index.php?action=design" method="post">
<?php
	
	csrf_token_html ();
	?>
	<table style="width: 100%;">
		<tr>
			<td><strong><?php
	
	echo TRANSLATION_DESIGN_OPTIONS_ENABLED;
	?> </strong></td>
			<td><input type="checkbox" name="disable_custom_layout_options"
				<?php
	
	if (! Settings::get ( "disable_custom_layout_options" )) {
		echo " checked";
	}
	?>></td>
		</tr>
		<tr>
			<td style="width: 300px;"><strong><?php
	
	echo TRANSLATION_TITLE_FORMAT;
	?> </strong></td>
			<td><input type="text" name="title_format"
				value="<?php
	
	echo $title_format;
	?>"></td>
		</tr>
		<tr>
			<td><strong><?php
	
	echo TRANSLATION_FRONTEND_DESIGN;
	?> </strong></td>
			<td><select name="theme" size=1>
			<?php
	
	foreach ( $allThemes as $th ) {
		?>
					<option value="<?php
		
		echo $th;
		?>"
						<?php
		if ($th === $theme)
			echo " selected"?>>
		<?php
		
		echo $th;
		?>
					</option>
					<?php
	}
	?>
			</select></td>
		</tr>

		<tr>
			<td><strong><?php
	
	echo TRANSLATION_MOBILE_DESIGN;
	?> </strong></td>
			<td><select name="mobile_theme" size=1>
					<option value=""
						<?php
	
	if (! $mobile_theme)
		echo " selected";
	?>>
						[
						<?php
	
	echo TRANSLATION_STANDARD;
	?>
						]
					</option>
					<?php
	
	foreach ( $allThemes as $th ) {
		?>
					<option value="<?php
		
		echo $th;
		?>"
						<?php
		if ($th === $mobile_theme)
			echo " selected"?>>
		<?php
		
		echo $th;
		?>
					</option>
					<?php
	}
	?>
			</select></td>
		</tr>
		<tr>
			<td><strong><?php
	
	translate ( "no_mobile_design_on_tablet" );
	?> </strong></td>
			<td><input type="checkbox" name="no_mobile_design_on_tablet"
				<?php
	
	if ($no_mobile_design_on_tablet) {
		echo " checked";
	}
	?>></td>
		</tr>
		<tr>
			<td><strong><?php
	
	echo TRANSLATION_EDITOR_SKIN;
	?> </strong></td>
			<td><select name="ckeditor_skin" size=1>
					<option value="moono"
						<?php
	if ($ckeditor_skin === "moono")
		echo " selected"?>>Moono</option>
					<option value="kama"
						<?php
	if ($ckeditor_skin === "kama")
		echo " selected"?>>Kama</option>
					<option value="office2013"
						<?php
	if ($ckeditor_skin === "office2013")
		echo " selected"?>>Office 2013</option>
			</select></td>
		</tr>
		<tr>
			<td><strong><?php
	
	echo TRANSLATION_FONT_FAMILY;
	?> </strong></td>
			<td><select name="default-font" id="default-font" size=1>
			<?php
	$font_amount = count ( $fonts );
	$i = 1;
	foreach ( $fonts as $key => $value ) {
		$selected = "";
		if ($default_font === $value)
			$selected = "selected";
		
		if (! in_array ( $default_font, $fonts ) and $i === $font_amount)
			$selected = "selected";
		if ($value != 'google') {
			echo '<optgroup style="font-family:' . $value . '; font-size:1.2em;">';
		} else {
			echo '<optgroup>';
		}
		echo "<option value=\"$value\" $selected>$key</option>";
		echo '</optgroup>';
		
		$i ++;
	}
	
	?>
			</select>
				<div id="google-fonts">
					<select name="google-font" size=1>
			<?php
	
	foreach ( $google_fonts as $myfont ) {
		if ($myfont == $google_font) {
			echo '<option value="' . htmlspecialchars ( $myfont ) . '" selected>' . htmlspecialchars ( $myfont ) . "</option>";
		} else {
			echo '<option value="' . htmlspecialchars ( $myfont ) . '">' . htmlspecialchars ( $myfont ) . "</option>";
		}
	}
	?>
			</select>
				</div></td>
		</tr>
		<tr>
			<td><strong><?php
	
	echo TRANSLATION_ZOOM;
	?> </strong>
			
			<td><select name="zoom">
			<?php
	for($i = 10; $i <= 200; $i += 10) {
		?>
					<option
						<?php
		if ($i === $zoom or ($i === 100 and $zoom === 0))
			echo " selected";
		?>
						value="<?php
		
		echo $i;
		?>">
		<?php
		
		echo $i;
		?>
						%
					</option>
					<?php
	}
	?>
			</select></td>
		</tr>
		<tr>
			<td><strong><?php
	
	echo TRANSLATION_FONT_SIZE;
	?> </strong>
			
			<td><select name="font-size">
			<?php
	foreach ( $font_sizes as $size ) {
		echo '<option value="' . $size . '"';
		if ($font_size == $size)
			echo " selected";
		echo ">";
		echo $size;
		echo "</option>";
	}
	?>
			</select></td>
		</tr>
		<tr>
			<td><strong><?php
	
	echo TRANSLATION_HEADER_BACKGROUNDCOLOR;
	?> </strong></td>
			<td><input name="header-background-color"
				class="color {hash:true,caps:true}"
				value="<?php
	
	echo real_htmlspecialchars ( Settings::get ( "header-background-color" ) );
	?>"></td>
		</tr>
		<tr>
			<td><strong><?php
	
	echo TRANSLATION_FONT_COLOR;
	?> </strong></td>
			<td><input name="body-text-color" class="color {hash:true,caps:true}"
				value="<?php
	
	echo real_htmlspecialchars ( Settings::get ( "body-text-color" ) );
	?>"></td>
		</tr>
		</tr>
		<tr>
			<td><strong><?php
	
	echo TRANSLATION_BACKGROUNDCOLOR;
	?> </strong></td>
			<td><input name="body-background-color"
				class="color {hash:true,caps:true}"
				value="<?php
	
	echo real_htmlspecialchars ( Settings::get ( "body-background-color" ) );
	?>"></td>
		</tr>
		<?php
	
	if ($acl->hasPermission ( "favicon" )) {
		?>
		<tr>
			<td><strong><?php translate("favicon");?></strong></td>
			<td>[<a href="index.php?action=favicon"><?php translate("upload_new_favicon");?></a>]
			</td>
		</tr>		
		<?php }?>
		<tr>
			<td><strong><?php
	
	translate ( "HTML5_VIDEO_WIDTH_100_PERCENT" );
	?> </strong>
			
			<td><input type="checkbox" name="video_width_100_percent"
				<?php
	
	if ($video_width_100_percent)
		echo " checked";
	?>
				value="video_width_100_percent"></td>
		</tr>


		<tr>
			<td><strong><?php
	
	translate ( "ADDITIONAL_MENUS" );
	?> </strong>
			
			<td><input type="text" name="additional_menus"
				value="<?php
	
	echo real_htmlspecialchars ( $additional_menus );
	?>"></td>
		</tr>

	</table>
	<p>
		<input type="submit" name="submit"
			value="<?php
	
	echo TRANSLATION_SAVE_CHANGES;
	?>" />
	</p>

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
$("#designForm").ajaxForm({beforeSubmit: function(e){
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

function onChangeDefaultFont(){
    var value = $("select#default-font").val();
    if(value == "google"){
       $("div#google-fonts").slideDown();
    } else {
       
       $("div#google-fonts").slideUp();
    }
}

$("select#default-font").change(onChangeDefaultFont);

</script>
<?php
}
?>
