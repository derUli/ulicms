<?php
$controller = ControllerRegistry::get();
$permissionChecker = new ACL();
if (! $permissionChecker->hasPermission("design")) {
    noPerms();
} else {
    $theme = Settings::get("theme");
    $additional_menus = Settings::get("additional_menus");
    $mobile_theme = Settings::get("mobile_theme");
    $allThemes = getThemesList();
    $fonts = $controller->getFontFamilys();
    $google_fonts = $controller->getGoogleFonts();
    $theme = Settings::get("theme");
    $additional_menus = Settings::get("additional_menus");
    $mobile_theme = Settings::get("mobile_theme");
    $default_font = Settings::get("default_font");
    $google_font = Settings::get("google-font");
    $title_format = htmlspecialchars(Settings::get("title_format"), ENT_QUOTES, "UTF-8");
    $font_size = Settings::get("font-size");
    $ckeditor_skin = Settings::get("ckeditor_skin");
    $video_width_100_percent = Settings::get("video_width_100_percent");
    $font_sizes = getFontSizes();
    $no_mobile_design_on_tablet = Settings::get("no_mobile_design_on_tablet");
    $modManager = new ModuleManager();
    $mobileDetectInstalled = in_array("Mobile_Detect", $modManager->getEnabledModuleNames());
    ?>
	<?php if ($default_font != "google") {?>
<style type="text/css">
div#google-fonts {
	display: none;
}
</style>
<?php }?>
<p>
	<a
		href="<?php echo ModuleHelper::buildActionURL("settings_categories");?>"
		class="btn btn-default btn-back"><i class="fas fa-arrow-left"></i> <?php translate("back")?></a>
</p>
<h1>
<?php translate("design");?>
</h1>
<?php
    
    echo ModuleHelper::buildMethodCallForm("DesignSettingsController", "save", array(), "post", array(
        "id" => "designForm"
    ));
    ?>
<table style="width: 100%;">
	<tr>
		<td><strong><?php translate("DESIGN_OPTIONS_ENABLED");?> </strong></td>
		<td><input type="checkbox" name="disable_custom_layout_options"
			<?php
    
    if (! Settings::get("disable_custom_layout_options")) {
        echo " checked";
    }
    ?>></td>
	</tr>
	<tr>
		<td style="width: 300px;"><strong><?php translate("title_format");?> </strong></td>
		<td><input type="text" name="title_format"
			value="<?php
    
    echo $title_format;
    ?>"></td>
	</tr>
	<tr>
		<td><strong><?php translate("frontend_design");?> </strong></td>
		<td><select name="theme" size=1>
			<?php
    
    foreach ($allThemes as $th) {
        ?>
					<option value="<?php
        
        echo $th;
        ?>"
					<?php
        if ($th === $theme) {
            echo " selected";
        }
        ?>>
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
		<td><strong><?php translate("mobile_design");?> </strong></td>
		<td>
			<p>
				<select name="mobile_theme" size=1>
					<option value=""
						<?php
    
    if (! $mobile_theme) {
        echo " selected";
    }
    ?>>
						[
						<?php translate("standard");?>
						]
					</option>
					<?php
    
    foreach ($allThemes as $th) {
        ?>
					<option value="<?php
        
        echo $th;
        ?>"
						<?php
        if ($th === $mobile_theme) {
            echo " selected";
        }
        ?>>
		<?php
        
        echo $th;
        ?>
					</option>
					<?php
    }
    ?>
			</select>
			</p>
			<div class="alert alert-warning fade in" id="mobile_detect_notice"
				data-installed="<?php echo strbool($mobileDetectInstalled);?>">
					
  <?php translate("mobile_detect_install_notice");?>
</div>
		</td>
	</tr>
	<tr>
		<td><strong><?php
    
    translate("no_mobile_design_on_tablet");
    ?> </strong></td>
		<td><input type="checkbox" name="no_mobile_design_on_tablet"
			<?php
    
    if ($no_mobile_design_on_tablet) {
        echo " checked";
    }
    ?>></td>
	</tr>
	<tr>
		<td><strong><?php translate("editor_skin");?> </strong></td>
		<td><select name="ckeditor_skin" size=1>
				<option value="moono"
					<?php
    if ($ckeditor_skin === "moono") {
        echo " selected";
    }
    ?>>Moono</option>
				<option value="kama"
					<?php
    if ($ckeditor_skin === "kama") {
        echo " selected";
    }
    ?>>Kama</option>
		</select></td>
	</tr>
	<tr>
		<td><strong><?php translate("font_family");?> </strong></td>
		<td><select name="default_font" id="default_font" size=1>
			<?php
    $font_amount = count($fonts);
    $i = 1;
    foreach ($fonts as $key => $value) {
        $selected = "";
        if ($default_font === $value) {
            $selected = "selected";
        }
        
        if (! faster_in_array($default_font, $fonts) and $i === $font_amount) {
            $selected = "selected";
        }
        if ($value != 'google') {
            echo '<optgroup style="font-family:' . $value . '; font-size:1.2em;">';
        } else {
            echo '<optgroup>';
        }
        echo "<option value=\"$value\" $selected>$key</option>";
        echo '</optgroup>';
        
        $i ++;
    }
    
    ?></select>
			<div id="google-fonts">
				<select name="google-font" size=1>
			<?php
    
    foreach ($google_fonts as $myfont) {
        if ($myfont == $google_font) {
            echo '<option value="' . htmlspecialchars($myfont) . '" selected>' . htmlspecialchars($myfont) . "</option>";
        } else {
            echo '<option value="' . htmlspecialchars($myfont) . '">' . htmlspecialchars($myfont) . "</option>";
        }
    }
    ?>
			</select>
				<div class="voffset3 alert alert-warning"><?php translate("google_fonts_privacy_warning");?></div>
			</div></td>
	</tr>
	<tr>
		<td><strong><?php translate("font_size");?> </strong>
		
		<td><select name="font-size">
			<?php
    foreach ($font_sizes as $size) {
        echo '<option value="' . $size . '"';
        if ($font_size == $size) {
            echo " selected";
        }
        echo ">";
        echo $size;
        echo "</option>";
    }
    ?>
			</select></td>
	</tr>
	<tr>
		<td><strong><?php translate("HEADER_BACKGROUNDCOLOR");?> </strong></td>
		<td><input name="header-background-color"
			class="jscolor {hash:true,caps:true}"
			value="<?php
    
    echo real_htmlspecialchars(Settings::get("header-background-color"));
    ?>"></td>
	</tr>
	<tr>
		<td><strong><?php translate("font_color");?> </strong></td>
		<td><input name="body-text-color"
			class="jscolor {hash:true,caps:true}"
			value="<?php
    
    echo real_htmlspecialchars(Settings::get("body-text-color"));
    ?>"></td>
	</tr>
	<tr>
		<td><strong><?php translate("BACKGROUNDCOLOR");?> </strong></td>
		<td><input name="body-background-color"
			class="jscolor {hash:true,caps:true}"
			value="<?php
    
    echo real_htmlspecialchars(Settings::get("body-background-color"));
    ?>"></td>
	</tr>
		<?php
    
    if ($permissionChecker->hasPermission("favicon")) {
        ?>
		<tr>
		<td><strong><?php translate("favicon");?></strong></td>
		<td><a href="index.php?action=favicon" class="btn btn-default"><i
				class="fas fa-file-image"></i> <?php translate("upload_new_favicon");?></a>
		</td>
	</tr>		
		<?php }?>
		<tr>
		<td><strong><?php
    
    translate("HTML5_VIDEO_WIDTH_100_PERCENT");
    ?> </strong>
		
		<td><input type="checkbox" name="video_width_100_percent"
			<?php
    
    if ($video_width_100_percent) {
        echo " checked";
    }
    ?>
			value="video_width_100_percent"></td>
	</tr>
	<tr>
		<td><strong><?php
    
    translate("ADDITIONAL_MENUS");
    ?> </strong>
		
		<td><input type="text" name="additional_menus"
			value="<?php
    
    echo real_htmlspecialchars($additional_menus);
    ?>"></td>
	</tr>
</table>
<p>
	<button type="submit" class="btn btn-primary voffset3" name="submit">
		<i class="fas fa-save"></i> <?php translate("save_changes");?></button>
</p>
<?php echo ModuleHelper::endForm();?>

<?php
    $translation = new JSTranslation();
    $translation->addKey("changes_was_saved");
    $translation->render();
    enqueueScriptFile("scripts/design.js");
    combinedScriptHtml();
    ?>
<?php
}
