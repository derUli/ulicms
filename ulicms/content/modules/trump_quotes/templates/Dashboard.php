<?php
$mainClass = ModuleHelper::getMainController ( "trump_quotes" );
?>
<h2 class="accordion-header"><?php translate("trump_quote");?></h2>
<div class="accordion-content"><?php echo $mainClass->render();?></div>