<?php

$classes = [
    "Script",
    "Link",
    "ListItem",
    "Input",
    "Button",
    "functions",
    "Form"
];

foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}
