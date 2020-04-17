<?php

use UliCMS\HTML\Input;

class InfoController extends MainClass {

    const CHANGELOG_URL = "https://raw.githubusercontent.com/derUli/ulicms/master/doc/changelog.txt";

    public function changelog() {
        ViewBag::set(
                "textarea_with_changelog",
                $this->getChangelogInTextarea()
        );
        
        HTMLResult(
                Template::executeModuleTemplate(
                        "core_info",
                        "changelog.php"
                )
        );
    }

    public function license() {
        HTMLResult(
                Template::executeModuleTemplate(
                        "core_info",
                        "license.php"
                )
        );
    }

    public function fetchChangelog() {
        $changelog = file_get_contents_wrapper(self::CHANGELOG_URL);

        return ($changelog ? trim($changelog) : get_translation("fetch_failed"));
    }

    public function getChangelogInTextarea() {
        return Input::textarea(
                        "changelog",
                        $this->fetchChangelog(),
                        10,
                        80,
                        [
                            "readonly" => "readonly"
                        ]
        );
    }

}
