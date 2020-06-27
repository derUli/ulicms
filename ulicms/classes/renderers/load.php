<?php

$classes = [
    "Csv",
    "Json",
    "Pdf",
    "PlainText"
];

foreach ($classes as $class) {
    require_once dirname(__file__) . "/{$class}Renderer.php";
}
