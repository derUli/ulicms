<?php

declare(strict_types=1);

$files = [
    'path',
    'url',
    'types',
    'packages',
    'encode',
    'request',
    'menus',
    'output',
    'constants',
    'modules',
    'designs',
    'cache',
    'languages',
    'environment',
    'comparisons',
    'pages',
    'minify',
    'csrf_token',
    'users_api',
    'string_functions',
    'network',
    'settings',
    'db_functions',
    'files',
    'file_get_contents_wrapper',
    'translation',
    'html5_media',
    'custom_data',
    'version_compare_functions'
];

foreach ($files as $file) {
    require_once dirname(__file__) . "/$file.php";
}
