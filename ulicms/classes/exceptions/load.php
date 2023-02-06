<?php

$classes = [
    "AccessDeniedException",
    "CorruptDownloadException",
    "NotImplementedException",
    "DatabaseException",
    "SqlException",
    "DatasetNotFoundException",
    "ConnectionFailedException",
    "FileNotFoundException",
    "SCSSCompileException",
    "UnknownContentTypeException",
    "NotSupportedException"
];

foreach ($classes as $class) {
    require_once dirname(__file__) . "/{$class}.php";
}
