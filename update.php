<?php 
// Require config and init-script
require_once "cms-config.php";
require_once "init.php";

setconfig("spamfilter_words_blacklist", 
"Casino;Euro Dice;Bingo;Cialis;Viagra;Penis;Enlargement;Drugstore");


//@unlink("update.php");

header("Location: admin/");
exit();

?>