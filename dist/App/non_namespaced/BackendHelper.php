<?php

declare(strict_types=1);

use App\HTML\Script;
use App\Utils\File;

class BackendHelper extends Helper
{
    // returns the current backend action or "home" if not specified
    public static function getAction(): string
    {
        return isset($_REQUEST["action"]) ? $_REQUEST["action"] : "home";
    }

    // set backend action parameter
    public static function setAction(string $action): void
    {
        $_REQUEST["action"] = $action;
        if (Request::isPost()) {
            $_POST["action"] = $action;
        } else {
            $_GET["action"] = $action;
        }
    }

    // add html editor scripts to the script queue
    public static function enqueueEditorScripts(): void
    {
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

    public static function getCKEditorSkins(): array
    {
        $skins = [];
        $dir = Path::resolve("ULICMS_ROOT/admin/ckeditor/skins");
        $folders = File::findAllDirs($dir);

        foreach ($folders as $folder) {
            $cssFile = "$folder/editor.css";
            if (is_file($cssFile)) {
                $skins[] = basename($folder);
            }
        }
        return $skins;
    }
}
