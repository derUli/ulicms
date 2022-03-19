<?php

declare(strict_types=1);

$files = [
    "cache",
    "environment",
    "comparisons",
    "pages",
    "minify",
    "csrf_token",
    "csv_writer",
    "users_api",
    "string_functions",
    "db_functions",
    "files",
    "file_get_contents_wrapper",
    "translation",
    "html5_media",
    "custom_data",
    "version_compare_functions",
    "legacy"
];

foreach ($files as $file) {
    require_once dirname(__file__) . "/$file.php";
}
