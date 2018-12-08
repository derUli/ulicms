<?php
$classes = array(
    "Audio",
    "Video"
);
foreach ($classes as $class) {
    include_once dirname(__file__) . "/$class.php";
}

