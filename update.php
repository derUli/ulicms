<?php 
// Require config and init-script
require_once "cms-config.php";
require_once "init.php";


mysql_query("UPDATE ".tbname("content"). " SET menu='bottom' WHERE menu='down'");

setconfig("spamfilter_words_blacklist", 
"Casino||Euro Dice||Bingo||Cialis||Viagra||Penis||Enlargement||Drugstore");

setconfig("empty_trash_days", 30);


$tpl_unten_filename = "templates/unten.php";

if(file_exists($tpl_unten_filename) and is_writable($tpl_unten_filename)){

   $template_unten = file_get_contents($tpl_unten_filename);
   $template_unten = str_replace('menu("down")', 
   'menu("bottom")' , $template_unten);
   $template_unten = str_replace("menu('down')", 
   "menu('bottom')" , $template_unten);
   
   $handle = fopen($tpl_unten_filename, "w");
   fwrite($handle, $template_unten);
   fclose($handle);
   
}


//@unlink("update.php");

header("Location: admin/");
exit();

?>
