<?php

$classes = [
    "Script",
    "Link",
    "ListItem",
    "Button",
    "functions",
    "Form"
];

foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}
