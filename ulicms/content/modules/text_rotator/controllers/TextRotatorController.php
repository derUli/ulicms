<?php

use UliCMS\HTML\ListItem;

class TextRotatorController extends MainClass {

    const MODULE_NAME = "text_rotator";

    public function getSettingsHeadline() {
        return get_translation("text_rotator");
    }

    public function settings() {
        return Template::executeModuleTemplate(
                        self::MODULE_NAME, "list.php");
    }

    public function adminHead() {
        enqueueStylesheet(
                ModuleHelper::buildRessourcePath(self::MODULE_NAME, "node_modules/animate.css/animate.min.css")
        );
        enqueueStylesheet(
                ModuleHelper::buildRessourcePath(self::MODULE_NAME, "node_modules/morphext/dist/morphext.css"));
        combinedStylesheetHtml();
    }

    private function currentPageContainsRotatingText() {
        $page = ContentFactory::getCurrentPage();
        return str_contains("[rotating_text=", $page->content);
    }

    public function enqueueFrontendStylesheets() {
        if ($this->currentPageContainsRotatingText()) {
            enqueueStylesheet(
                    ModuleHelper::buildRessourcePath(self::MODULE_NAME, "node_modules/animate.css/animate.min.css")
            );
            enqueueStylesheet(
                    ModuleHelper::buildRessourcePath(self::MODULE_NAME, "node_modules/morphext/dist/morphext.css"));
        }
    }

    public function enqueueFrontendFooterScripts() {
        if ($this->currentPageContainsRotatingText()) {
            enqueueScriptFile(
                    ModuleHelper::buildRessourcePath(self::MODULE_NAME, "node_modules/morphext/dist/morphext.min.js"));
            enqueueScriptFile(
                    ModuleHelper::buildRessourcePath(self::MODULE_NAME, "js/text_rotator.js")
            );
        }
    }

    public function preview() {
        $rotating_text = new RotatingText();
        $words = Request::getVar("words", "", "str");
        $separator = Request::getVar("separator", ",", "str");
        $speed = Request::getVar("speed", 2000, "int");
        $animation = Request::getVar("animation", "", "str");

        $rotating_text->setWords($words);
        $rotating_text->setSeparator($separator);
        $rotating_text->setSpeed($speed);
        $rotating_text->setAnimation($animation);

        HtmlResult($rotating_text->getHtml());
    }

    public function beforeContentFilter($html) {
        $texts = RotatingText::getAll();
        foreach ($texts as $text) {
            if (str_contains($text->getShortcode(), $html)) {
                $html = str_replace($text->getShortcode(), $text->getHtml(), $html);
            }
        }
        return $html;
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

    public function getAnimationItems() {
        $fx = [
            "attention_seekers" => [
                "bounce",
                "flash",
                "pulse",
                "rubberBand",
                "shake",
                "swing",
                "tada",
                "wobble"
            ],
            "bouncing_entrances" => [
                "bounceIn",
                "bounceInDown",
                "bounceInLeft",
                "bounceInRight",
                "bounceInUp"
            ],
            "fading_entrances" => [
                "fadeIn",
                "fadeInDown",
                "fadeInDownBig",
                "fadeInLeft",
                "fadeInLeftBig",
                "fadeInRight",
                "fadeInRightBig",
                "fadeInUp",
                "fadeInUpBig"
            ],
            "flipping_entrances" => [
                "flip",
                "flipInX",
                "flipInY"
            ],
            "rotating_entrances" => [
                "rotateIn",
                "rotateInDownLeft",
                "rotateInDownRight",
                "rotateInUpLeft",
                "rotateInUpRight"
            ],
            "zoom_entrances" => [
                "zoomIn",
                "zoomInDown",
                "zoomInLeft",
                "zoomInRight",
                "zoomInUp"
            ],
            "others" => [
                "lightSpeedIn",
                "rollIn"
            ]
        ];
        $items = [];
        foreach ($fx as $type => $effects) {
            foreach ($effects as $effect) {
                $translatedType = get_translation("fx_type_{$type}");
                $item = new ListItem($effect,
                        "$effect ({$translatedType})");
                $items[] = $item;
            }
        }
        return $items;
    }

    public function deletePost() {

        $id = Request::getVar("id", null, "int");
        if (!$id) {
            ExceptionResult(get_translation("not_found"), HttpStatusCode::NOT_FOUND);
        }
        $rotatingText = new RotatingText($id);
        $rotatingText->delete();
        Response::sendHttpStatusCodeResultIfAjax(
                HttpStatusCode::OK, ModuleHelper::buildAdminUrl(self::MODULE_NAME)
        );
    }

}
