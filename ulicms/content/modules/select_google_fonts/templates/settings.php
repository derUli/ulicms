<?php
$controller = ControllerRegistry::get("DesignSettingsController");
$fonts = $controller->getGoogleFonts();
?>
<ol>
<?php foreach($fonts as $font){
  ?>
<li><span style="font-family:<?php echo Template::escape($font);?>">Franz jagt im komplett verwahrlosten Taxi quer durch Bayern
.</span></li>
<?php
}
?>

</ol>
