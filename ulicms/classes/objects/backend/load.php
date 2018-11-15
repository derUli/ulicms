<?php
$classes = array(
    "AdminMenu",
    "MenuEntry",
    "BackendPageRenderer"
);
foreach ($classes as $class) {
    include_once dirname(__file__) . "/$class.php";
}

