<?php
$classes = array(
    "AdminMenu",
    "MenuEntry"
);
foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}

