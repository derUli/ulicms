<?php

$classes = array(
    "Controller",
    "Helper",
    "MainClass",
    "Model"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}

