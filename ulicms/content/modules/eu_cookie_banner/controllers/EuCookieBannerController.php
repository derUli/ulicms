<?php

use zz\Html\HTMLMinify;

class EuCookieBannerController extends MainClass {

    const MODULE_NAME = "eu_cookie_banner";

    public function head() {
        if (intval(Settings::get("eu_cookie_banner/include_default_css")) === 1) {
            enqueueStylesheet(ModuleHelper::buildRessourcePath(
                            self::MODULE_NAME,
                            "css/cookie_banner.css"
                    )
            );
        }
        combinedStylesheetHtml();
    }

    public function getSettingsHeadline() {
        return get_translation("eu_cookie_banner");
    }

    public function settings() {
        return Template::executeModuleTemplate(self::MODULE_NAME,
                        "settings.php");
    }

    public function beforeContent() {
        return Template::executeModuleTemplate(self::MODULE_NAME,
                        "cookie_banner.php");
    }

    public function saveSettingsPost() {

        $this->applyLanguageSpecificSettings();
        Settings::set(
                "eu_cookie_banner/html_code",
                Request::getVar(
                        "eu_cookie_banner/html_code"
                )
        );

        Settings::set(
                "eu_cookie_banner/include_default_css",
                Request::getVar(
                        "eu_cookie_banner/include_default_css",
                        "0",
                        "bool"
                )
        );

        Response::redirect(
                ModuleHelper::buildAdminURL(
                        self::MODULE_NAME,
                        "save=1"
                )
        );
    }

    private function applyLanguageSpecificSettings() {
        $languages = getAllLanguages();
        foreach ($languages as $language) {
            Settings::set(
                    "eu_cookie_banner/help_text_{$language}",
                    Request::getVar("eu_cookie_banner/help_text_{$language}")
            );

            Settings::set(
                    "eu_cookie_banner/accept_{$language}",
                    Request::getVar("eu_cookie_banner/accept_{$language}")
            );

            Settings::set(
                    "eu_cookie_banner/reject_{$language}",
                    Request::getVar("eu_cookie_banner/reject_{$language}"
                    )
            );
        }
    }

    public function getHtmlCode() {
        HTMLResult(
                Settings::get("eu_cookie_banner/html_code"),
                HttpStatusCode::OK,
                HTMLMinify::OPTIMIZATION_ADVANCED
        );
    }

    public function frontendFooter() {
        enqueueScriptFile(
                ModuleHelper::buildRessourcePath(
                        self::MODULE_NAME,
                        "node_modules/cookies-eu-banner/dist/cookies-eu-banner.js"
                )
        );

        enqueueScriptFile(
                ModuleHelper::buildRessourcePath(
                        self::MODULE_NAME,
                        "js/main.js"
                )
        );

        combinedScriptHtml();
    }

}
