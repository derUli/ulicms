<?php

use UliCMS\HTML\Input;
use Michelf\MarkdownExtra;
use zz\Html\HTMLMinify;

class InfoController extends MainClass {

    const CHANGELOG_URL = "https://raw.githubusercontent.com/derUli/ulicms/master/doc/changelog.txt";

    public function _fetchChangelog() {
        $changelog = file_get_contents_wrapper(self::CHANGELOG_URL);
        return ($changelog ? trim($changelog) : get_translation("fetch_failed"));
    }

    public function _getComposerLegalInfo(): string {
        $legalFile = Path::resolve("ULICMS_ROOT/licenses.md");
        $lastModified = filemtime($legalFile);

        $cacheFile = Path::resolve("ULICMS_CACHE/legal-{$lastModified}.html");

        if (file_exists($cacheFile)) {
            return file_get_contents($cacheFile);
        }

        $legalText = file_get_contents($legalFile);

        $parser = new MarkdownExtra;
        $parser->hard_wrap = true;
        $parsed = $parser->transform($legalText);

        file_put_contents($cacheFile, $parsed);

        return $parsed;
    }

    public function _getNpmLegalInfo(): array {
        $legalJson = file_get_contents(
                Path::resolve("ULICMS_ROOT/licenses.json")
        );
        return json_decode($legalJson);
    }

}
