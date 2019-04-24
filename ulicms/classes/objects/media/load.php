<?php

$classes = array(
    "Audio",
    "Video"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}

