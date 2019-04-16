<?php

class BackendHelper extends Helper {

    public static function formatDatasetCount($count) {
        if ($count == 1) {
            translate("ONE_DATASET_FOUND");
        } else {
            translate("X_DATASETS_FOUND", array(
                "%x" => $count
            ));
        }
    }

    public static function getAction() {
        if (isset($_REQUEST["action"])) {
            return $_REQUEST["action"];
        } else {
            return "home";
        }
    }

    public static function setAction($action) {
        $_REQUEST["action"] = $action;
        $_GET["action"] = $action;
        if (Request::isPost()) {
            $_POST["action"] = $action;
        }
    }

    public static function enqueueEditorScripts() {
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
