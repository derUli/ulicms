<?php

$classes = array(
    "AllowedTags"
);

foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}
