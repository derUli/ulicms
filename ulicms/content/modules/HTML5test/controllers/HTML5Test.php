<?php

class HTML5Test extends Controller {

    const MODULE_NAME = "HTML5test";

    public function settings() {
        return Template::executeModuleTemplate(self::MODULE_NAME, "iframe.php");
    }

    public function getSettingsHeadline() {
        return get_translation("html5test_title");
    }

}
