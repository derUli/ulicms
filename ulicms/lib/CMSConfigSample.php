<?php

// This file file loads the configuration based on the environment
// Set the environment variable ULICMS_ENVIRONMENT to change the configuration environment
// If ULICMS_ENVIRONMENT is not set UliCMS fallbacks to "default" Environment
// configuration files are now located in ULICMS_ROOT/content/configurations
// Don't edit this file!
$environment = getenv("ULICMS_ENVIRONMENT") ? getenv("ULICMS_ENVIRONMENT") : "default";
$environment = basename($environment);

$file = dirname(__FILE__) . "/content/configurations/{$environment}.php";

if (!is_file($file)) {
    header("HTTP/1.1 500 Internal Server Error ");
    echo "Configuration file for environment {$environment} not found.";
    exit();
}

require_once $file;
