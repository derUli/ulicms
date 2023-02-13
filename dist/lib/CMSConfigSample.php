<?php

declare(strict_types=1);

function_exists('get_environment') or exit('no direct script access allowed');

// This file file loads the configuration based on the environment
// Set the environment variable ULICMS_ENVIRONMENT to change the configuration environment
// If ULICMS_ENVIRONMENT is not set UliCMS fallbacks to "default" Environment
// configuration files are now located in ULICMS_ROOT/content/configurations
// Don't edit this file!
$environment = basename(get_environment());

$file = ULICMS_CONFIGURATIONS . "/{$environment}.php";

// If there is no config file for the environment show an error
if (!is_file($file)) {
    header('HTTP/1.1 500 Internal Server Error');
    echo "Configuration file for environment {$environment} not found.";
    exit();
}

require $file;
