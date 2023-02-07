<?php

$classes = [
    "GroupCollection",
    "Session"
];

foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}
