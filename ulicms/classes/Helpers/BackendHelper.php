<?php

declare(strict_types=1);

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use UliCMS\HTML\Script;

/**
 * Helper methods for admin backend area
 */
class BackendHelper extends Helper {

    /**
     * Returns the current action or "home" if not specified
     * @return string
     */
    public static function getAction(): string {
        return isset($_REQUEST["action"]) ? $_REQUEST["action"] : "home";
    }

    /**
     * Set action
     * @param string $action
     * @return void
     */
    public static function setAction(string $action): void {
        $_REQUEST["action"] = $action;
        if (Request::isPost()) {
            $_POST["action"] = $action;
        } else {
            $_GET["action"] = $action;
        }
    }

    /**
     * Add html editor scripts to the script queue
     * @return void
     */
    public static function enqueueEditorScripts(): void {
        // ckeditor is huge so embed it only if this is the user'S preferred html editor
        if (get_html_editor() == "ckeditor") {
            echo Script::fromFile("ckeditor/ckeditor.js");
            enqueueScriptFile(ModuleHelper::buildRessourcePath(
                            "core_content",
                            "js/pages/init-ckeditor.js"
            ));
        }

        enqueueScriptFile(
                "../node_modules/codemirror-minified/lib/codemirror.js"
        );
        enqueueScriptFile(
                "../node_modules/codemirror-minified/mode/php/php.js"
        );
        enqueueScriptFile(
                "../node_modules/codemirror-minified/mode/xml/xml.js"
        );
        enqueueScriptFile(
                "../node_modules/codemirror-minified/mode/javascript/javascript.js"
        );
        enqueueScriptFile(
                "../node_modules/codemirror-minified/mode/clike/clike.js"
        );
        enqueueScriptFile(
                "../node_modules/codemirror-minified/mode/css/css.js"
        );

        enqueueScriptFile(ModuleHelper::buildRessourcePath(
                        "core_content",
                        "js/pages/init-codemirror.js"
        ));
    }

    /**
     * Get list of installed CKEditor skin
     * @return array
     */
    public static function getCKEditorSkins(): array {
        $skins = [];
        $dir = Path::resolve("ULICMS_ROOT/admin/ckeditor/skins");
        $folders = find_all_folders($dir);

        foreach ($folders as $folder) {
            $cssFile = "$folder/editor.css";
            if (file_exists(($cssFile))) {
                $skins[] = basename($folder);
            }
        }
        return $skins;
    }

}
