<?php
$controller = ControllerRegistry::get ( getModuleMeta ( "fortune", "main_class" ) );
?>
<h2 class="accordion-header"><?php
translate ( "fortune" );
?></h2>
<div class="accordion-content">
<?php echo $controller->render();?>
</div>