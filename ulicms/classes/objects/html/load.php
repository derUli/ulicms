<?php

$classes = array(
    "Template",
    "Style",
    "Script",
    "Link",
    "ListItem",
    "Input",
    "Alert",
    "Button",
    "functions",
    "Form"
);
foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}
