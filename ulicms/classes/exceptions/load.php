<?php

$classes = [
    "AccessDeniedException",
    "CorruptDownloadException",
    "NotImplementedException",
    "SqlException",
    "FileNotFoundException",
    "ArgumentNullException",
    "SCSSCompileException",
    "UnknownContentTypeException"
];

foreach ($classes as $class) {
    require_once dirname(__file__) . "/{$class}.php";
}
