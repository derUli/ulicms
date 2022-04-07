<?php

$classes = array(
    "Group",
    "User",
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
