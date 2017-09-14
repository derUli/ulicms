<?php
$controller = ControllerRegistry::get ( "DesignSettingsController" );
$fonts = $controller->getGoogleFonts ();
?>

<link rel="stylesheet" type="text/css"
	href="<?php echo ModuleHelper::buildModuleRessourcePath("select_google_fonts", "css/style.css");?>" />
<?php foreach($fonts as $font){ ?>
<link rel="stylesheet" type="text/css"
	href="//fonts.googleapis.com/css?family=<?php Template::escape($font);?>" />
<?php
}
