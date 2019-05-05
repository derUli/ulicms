<?php

class TextRotatorController extends MainClass {

    const MODULE_NAME = "text_rotator";

    public function getSettingsHeadline() {
        return get_translation("text_rotator");
    }

    public function settings() {
        return Template::executeModuleTemplate(
                        self::MODULE_NAME, "list.php");
    }

}
