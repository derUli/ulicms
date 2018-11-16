<?php
$classes = array(
    "CacheUtil",
    "File",
    "Path"
);
foreach ($classes as $class) {
    include_once dirname(__file__) . "/$class.php";
}

