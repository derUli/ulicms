<?php

use UliCMS\HTML\Input;

class InfoController extends MainClass {

    const CHANGELOG_URL = "https://raw.githubusercontent.com/derUli/ulicms/master/doc/changelog.txt";

    public function changelog() {
        HTMLResult("<h1 class=\"modal-headline\">Changelog</h1>
            <hr/>
            {$this->getChangelogInTextarea()}"
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
                        15,
                        80,
                        [
                            "readonly" => "readonly"
                        ]
        );
    }

}
