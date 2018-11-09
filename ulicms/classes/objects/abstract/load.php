<?php
$classes = array(
    "Controller",
    "Helper",
    "MainClass",
    "Model"
);
foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}

