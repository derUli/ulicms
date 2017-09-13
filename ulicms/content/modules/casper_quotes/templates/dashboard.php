<?php
$controller = ControllerRegistry::get ( getModuleMeta ( "casper_quotes", "main_class" ) );
?>
<h2 class="accordion-header"><?php
translate ( "casper_quotes" );
?></h2>
<div class="accordion-content">
<?php echo $controller->render();?>
</div>