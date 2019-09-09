<?php

declare(strict_types=1);

class BackendHelper extends Helper {

    // Format dataset count singular / prual
    // one dataset found or 123 datasets found
    public static function formatDatasetCount(int $count): void {
        if ($count == 1) {
            translate("ONE_DATASET_FOUND");
        } else {
            translate("X_DATASETS_FOUND", array(
                "%x" => $count
            ));
        }
    }

    // returns the current backend action or "home" if not specified
    public static function getAction(): string {
        return isset($_REQUEST["action"]) ? $_REQUEST["action"] : "home";
    }

    // set backend action parameter
    public static function setAction(string $action): void {
        $_REQUEST["action"] = $action;
        $_GET["action"] = $action;
        if (Request::isPost()) {
            $_POST["action"] = $action;
        }
    }

    // add html editor scripts to the script queue
    public static function enqueueEditorScripts(): void {
        // ckeditor is huge so embed it only if this is the user'S preferred html editor
        if (get_html_editor() == "ckeditor") {
            enqueueScriptFile(ModuleHelper::buildRessourcePath("core_content", "js/pages/init-ckeditor.js"));
        }

        enqueueScriptFile("../node_modules/codemirror-minified/lib/codemirror.js");
        enqueueScriptFile("../node_modules/codemirror-minified/mode/php/php.js");
        enqueueScriptFile("../node_modules/codemirror-minified/mode/xml/xml.js");
        enqueueScriptFile("../node_modules/codemirror-minified/mode/javascript/javascript.js");
        enqueueScriptFile("../node_modules/codemirror-minified/mode/clike/clike.js");
        enqueueScriptFile("../node_modules/codemirror-minified/mode/css/css.js");

        enqueueScriptFile(ModuleHelper::buildRessourcePath("core_content", "js/pages/init-codemirror.js"));
    }

}
