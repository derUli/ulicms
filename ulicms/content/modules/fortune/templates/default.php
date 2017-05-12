<?php
$controller = ControllerRegistry::get ( getModuleMeta ( "fortune", "main_class" ) );
?>
<div class="fortune">
<?php
$fortune = $controller->getRandomFortune ();
echo nl2br ( $fortune );
?>
	</div>