<?php

$classes = array(
    "MenuEntry",
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
