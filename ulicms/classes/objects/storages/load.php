<?php

$classes = array(
    "SettingsCache",
    "Vars",
    "ViewBag"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
