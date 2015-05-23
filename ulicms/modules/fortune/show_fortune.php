<h1>Glückskeks</h1>
<?php
include_once getModulePath ("fortune") . "fortune_lib.php";
$fortune = getRandomFortune ();
echo nl2br ($fortune);
?>