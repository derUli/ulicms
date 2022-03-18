<?php

$classes = array(
    "Template",
    "functions",
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
