<?php
$classes = array(
    "Style",
    "Script",
    "Link",
    "ListItem",
    "Input",
    "functions"
);
foreach ($classes as $class) {
    include_once dirname(__file__) . "/$class.php";
}

