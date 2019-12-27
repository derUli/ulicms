<?php

declare(strict_types=1);

$files = array(
    "polyfill",
    "path",
    "url",
    "types",
    "packages",
    "encode",
    "request",
    "menus",
    "output",
    "constants",
    "arrays",
    "modules",
    "designs",
    "cache",
    "languages",
    "environment",
    "comparisons",
    "pages",
    "minify",
    "csrf_token",
    "csv_writer",
    "users_api",
    "string_functions",
    "network",
    "settings",
    "db_functions",
    "files",
    "file_get_contents_wrapper",
    "translation",
    "html5_media"
);

foreach ($files as $file) {
    require_once dirname(__file__) . "/$file.php";
}