<?php

$classes = array(
    "HttpStatusCode",
    "AllowedTags"
);

foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
