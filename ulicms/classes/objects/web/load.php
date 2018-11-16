<?php
$classes = array(
    "HttpStatusCode",
    "Mailer",
    "Request",
    "Response"
);
foreach ($classes as $class) {
    include_once dirname(__file__) . "/$class.php";
}

