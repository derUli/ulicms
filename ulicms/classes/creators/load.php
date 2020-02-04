<?php

$classes = [
    "CSVCreator",
    "JSONCreator",
    "PDFCreator",
    "PlainTextCreator"
];

foreach ($classes as $class) {
    require_once dirname(__file__) . "/$class.php";
}

