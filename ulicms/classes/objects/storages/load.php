<?php

$classes = array(
    "Vars",
    "ViewBag"
);
foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}
