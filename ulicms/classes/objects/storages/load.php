<?php
$classes = array(
    "Flags",
    "SettingsCache",
    "Vars",
    "ViewBag"
);
foreach ($classes as $class) {
    include_once dirname(__file__) . "/$class.php";
}

