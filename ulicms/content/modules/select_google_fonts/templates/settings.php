<?php
$controller = ControllerRegistry::get("DesignSettingsController");
$fonts = $controller->getGoogleFonts();
?>
<!-- @TODO: CSS in Datei auslagern-->
<ol style="list-style:none">
<?php foreach($fonts as $font){
  ?>
<li><input type="radio" name="google_font" id="label-<?php echo md5($font);?>">
  <label for="label-<?php echo md5($font);?>" style="font-family:<?php echo Template::escape($font);?>; font-size:110%;">Franz jagt im komplett verwahrlosten Taxi quer durch Bayern.</label></li>
<?php
}
?>

</ol>
