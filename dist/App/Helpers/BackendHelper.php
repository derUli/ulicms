<?php

declare(strict_types=1);

namespace App\Helpers;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Constants\HtmlEditor;
use App\HTML\Script;
use App\Utils\File;
use Path;
use Request;

use function enqueueScriptFile;
use function get_html_editor;

/**
 * Backend utilities
 */
abstract class BackendHelper extends Helper {
    /**
     * Get the current backend action
     * @return string
     */
    public static function getAction(): string {
        return $_REQUEST['action'] ?? 'home';
    }

    /**
     * Set the current backend action
     * @param string $action
     * @return void
     */
    public static function setAction(string $action): void {
        $_REQUEST['action'] = $action;

        if (Request::isPost()) {
            $_POST['action'] = $action;
        } else {
            $_GET['action'] = $action;
        }
    }

    /**
     * Enqueue HTML editor scripts
     * @return void
     */
    public static function enqueueEditorScripts(): void {
        // ckeditor is huge so embed it only if this is the user'S preferred html editor
        if (get_html_editor() == HtmlEditor::CKEDITOR) {
            echo Script::fromFile('ckeditor/ckeditor.js');

            enqueueScriptFile(\App\Helpers\ModuleHelper::buildRessourcePath(
                'core_content',
                'js/pages/init-ckeditor.js'
            ));
        }

        enqueueScriptFile(
            '../node_modules/codemirror-minified/lib/codemirror.js'
        );

        enqueueScriptFile(
            '../node_modules/codemirror-minified/mode/php/php.js'
        );

        enqueueScriptFile(
            '../node_modules/codemirror-minified/mode/xml/xml.js'
        );

        enqueueScriptFile(
            '../node_modules/codemirror-minified/mode/javascript/javascript.js'
        );

        enqueueScriptFile(
            '../node_modules/codemirror-minified/mode/clike/clike.js'
        );

        enqueueScriptFile(
            '../node_modules/codemirror-minified/mode/css/css.js'
        );

        enqueueScriptFile(\App\Helpers\ModuleHelper::buildRessourcePath(
            'core_content',
            'js/pages/init-codemirror.js'
        ));
    }

    /**
     * Get CKEditor Skin.
     * @return string[]
     */
    public static function getCKEditorSkins(): array {
        $skins = [];
        $dir = \App\Utils\Path::resolve('ULICMS_ROOT/admin/ckeditor/skins');
        $folders = File::findAllDirs($dir);

        foreach ($folders as $folder) {
            $cssFile = "{$folder}/editor.css";
            if (is_file($cssFile)) {
                $skins[] = basename($folder);
            }
        }
        return $skins;
    }
}
