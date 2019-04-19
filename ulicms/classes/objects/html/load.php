<?php

$classes = array(
    "Style",
    "Script",
    "Link",
    "ListItem",
    "Input",
    "Alert",
    "functions"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}

