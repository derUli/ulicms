<?php
$environment = getenv("ULICMS_ENVIRONMENT") ? getenv("ULICMS_ENVIRONMENT") : "default";
$environment = basename($environment);

$file = dirname(__FILE__) . "/content/configurations/{$environment}.php";

if (! is_file($file)) {
    header("HTTP/1.1 500 Internal Server Error ");
    echo "Configuration file for environment {$environment} not found.";
    exit();
}

include_once $file;