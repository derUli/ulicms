<?php
// Post Install Script für Blog Package 6.0.2
if(!function_exists("setconfig"))
     include "init.php";

echo "<p>Lege Datenbankstruktur an</p>";
require_once getModulePath("blog") . "blog_main.php";
blog_check_installation();
echo "<p>fertig</p>";
?>
