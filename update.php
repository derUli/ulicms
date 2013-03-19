<?php 
// Require config and init-script
require_once "cms-config.php";
require_once "init.php";


mysql_query("UPDATE ".tbname("content"). " SET menu='bottom' WHERE menu='down'");

setconfig("spamfilter_words_blacklist", 
"Casino||Euro Dice||Bingo||Cialis||Viagra||Penis||Enlargement||Drugstore");

setconfig("empty_trash_days", 30);


//@unlink("update.php");

header("Location: admin/");
exit();

?>