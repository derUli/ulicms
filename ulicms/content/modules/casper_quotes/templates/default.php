<?php
$controller = ControllerRegistry::get ( getModuleMeta ( "fortune2", "main_class" ) );
?>
<div class="casper-quote">
<?php
$fortune = $controller->getRandomFortune ();
echo nl2br ( $fortune );
?>
	</div>
