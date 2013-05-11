<?php 

if(!function_exists("setconfig"))
   include_once "init.php";
deleteconfig("body-background-color");
setconfig("body-background-color", "#ffffff");

deleteconfig("body-text-color");
setconfig("body-text-color", "#4b4b4b");

deleteconfig("header-background-color");
setconfig("header-background-color", "#00000");


deleteconfig("default-font");
setconfig("default-font", 'font-family:"Palatino Linotype", "Book Antiqua", Palatino, serif');
?>
<p><strong>Theme-Variablen:</strong><br/>
body-background-color<br/>
body-text-color<br/>
header-background-color<br/>
default-font</p>
<p>Diese können über den Expertenmodus der Einstellungen geändert werden</p>