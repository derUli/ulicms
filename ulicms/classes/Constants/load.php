<?php

$classes = array(
    "HttpStatusCode",
    "EmailModes",
    "AllowedTags",
    "ButtonType"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}
