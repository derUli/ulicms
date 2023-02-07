<?php

$classes = array(
    "AllowedTags"
);

foreach ($classes as $class) {
    require_once dirname(__FILE__) . "/$class.php";
}
