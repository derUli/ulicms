<?php

$classes = array(
    "AdminMenu",
    "MenuEntry",
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
