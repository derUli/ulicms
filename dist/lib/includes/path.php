<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

function getTemplateDirPath(
    string $sub = 'default',
    bool $abspath = false
): string {
    if ($abspath) {
        $templateDir = \App\Utils\Path::resolve(
            'ULICMS_ROOT/content/templates/'
        ) . '/';
    } elseif (is_admin_dir()) {
        $templateDir = '../content/templates/';
    } else {
        $templateDir = 'content/templates/';
    }

    $templateDir = $templateDir . $sub . '/';
    return $templateDir;
}

// XXX: What's the meaning of this method?
// is this method mandatory or is there an other method
// which can be used as replacement?
function getModuleAdminSelfPath(): string {
    return _esc(get_request_uri());
}

function getModulePath($module, $abspath = false): string {
    if ($abspath) {
        return \App\Utils\Path::resolve(
            "ULICMS_ROOT/content/modules/{$module}"
        ) . '/';
    }

    // Frontend Directory
    if (! is_admin_dir()) {
        $module_folder = 'content/modules/';
    } // Backend Directory
    else {
        $module_folder = '../content/modules/';
    }

    return $module_folder . $module . '/';
}

function getModuleAdminFilePath($module): string {
    return getModulePath($module, true) . 'admin.php';
}
