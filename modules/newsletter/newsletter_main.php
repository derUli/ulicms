<?php 

include getModulePath("newsletter")."newsletter_install.php";
newsletter_check_install();

function newsletter_render(){
   newsletter_check_install();
   return "Hello World!";
}




?>