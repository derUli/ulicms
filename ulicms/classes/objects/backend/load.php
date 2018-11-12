<?php
$classes = array(
    "AdminMenu",
    "MenuEntry"
);
foreach ($classes as $class) {
    include_once dirname(__file__) . "/$class.php";
}

