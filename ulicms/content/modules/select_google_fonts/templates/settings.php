<?php
$controller = ControllerRegistry::get ( "DesignSettingsController" );
$fonts = $controller->getGoogleFonts ();
$currentGoogleFont = Settings::get ( "google-font" );
?>
<?php


if (Request::hasVar ( "save" )) {
	?>
<div class="alert alert-success alert-dismissible fade in" role="alert">
	<button type="button" class="close" data-dismiss="alert"
		aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
  <?php translate("changes_was_saved");?>
</div>

<?php }?>
<?php echo ModuleHelper::buildMethodCallForm("SelectGoogleFontsController", "save");?>
<!-- @TODO: CSS in Datei auslagern-->
<ol class="google-fonts">
<?php

foreach ( $fonts as $font ) {
	?>
<li><input type="radio" name="google-font"
		id="label-<?php echo md5($font);?>"
		value="<?php Template::escape($font);?>"
		<?php
	
if ($font == $currentGoogleFont) {
		?> checked <?php
	}
	?>> <label for="label-<?php echo md5($font);?>" style="font-family:<?php echo Template::escape($font);?>; font-size:110%;">
    <?php translate("font_preview_text");?></label></li>
<?php
}
?>
</ol>
<button type="submit" class="btn btn-warning"><?php translate("use_this_font");?></button>
</form>
