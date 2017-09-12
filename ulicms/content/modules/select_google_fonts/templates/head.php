<?php
$controller = ControllerRegistry::get("DesignSettingsController");
$fonts = $controller->getGoogleFonts();

foreach($fonts as $font){
  ?>
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=<?php Template::escape($font);?>"/>
  <?php
}
