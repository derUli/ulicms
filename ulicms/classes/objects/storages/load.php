<?php
$classes = array(
    "Flags",
    "SettingsCache",
    "Vars",
    "ViewBag"
);
foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}

