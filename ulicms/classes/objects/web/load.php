<?php

$classes = array(
    "Mailer",
    "Request",
    "Response"
);
foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}

