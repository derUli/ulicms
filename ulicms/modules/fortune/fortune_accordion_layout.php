<h2 class="accordion-header"><?php translate("fortune");?></h2>
<div class="accordion-content">
<?php
 include_once getModulePath("fortune") . "fortune_lib.php";
 $fortune = getRandomFortune();
 echo nl2br($fortune);
?>
</div>