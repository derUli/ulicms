<?php

$classes = array(
    "CacheUtil",
    "File",
    "Path"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}

