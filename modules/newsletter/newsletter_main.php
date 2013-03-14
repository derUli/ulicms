<?php 

include getModulePath("newsletter")."newsletter_install.php";

function newsletter_render(){
   newsletter_check_install();
   return "Hello World!";
}




?>