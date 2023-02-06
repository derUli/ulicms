<?php

$classes = array(
    "AllowedTags",
    "HttpStatusCode",
);

foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
