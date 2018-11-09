<?php
$classes = array(
    "CacheUtil",
    "File",
    "Path"
);
foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}

