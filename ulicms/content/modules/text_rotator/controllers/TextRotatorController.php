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

    public function savePost() {
        $id = Request::getVar("id", null, "int");

        $rotating_text = new RotatingText();
        if ($id) {
            $rotating_text->loadByID($id);
        }
        $words = Request::getVar("words", "", "str");
        $separator = Request::getVar("separator", ",", "str");
        $speed = Request::getVar("speed", 2000, "int");
        $animation = Request::getVar("animation", "", "str");

        $rotating_text->setWords($words);
        $rotating_text->setSeparator($separator);
        $rotating_text->setSpeed($speed);
        $rotating_text->setAnimation($animation);

        $rotating_text->save();
        Response::redirect(
                ModuleHelper::buildAdminURL(
                        self::MODULE_NAME
                )
        );
    }

}
