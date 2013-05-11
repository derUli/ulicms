<?php 

if(!function_exists("setconfig"))
   include_once "init.php";
deleteconfig("body-background-color");
setconfig("body-background-color", "#ffffff");

deleteconfig("body-text-color");
setconfig("body-text-color", "#3a3939");

deleteconfig("header-background-color");
setconfig("header-background-color", "#00000");


deleteconfig("default-font");
setconfig("default-font", 'font-family:"Palatino Linotype", "Book Antiqua", Palatino, serif');
?>