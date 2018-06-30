<?php
$classes = array(
    "HttpStatusCode",
    "Mailer",
    "Request",
    "Response"
);
foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}

