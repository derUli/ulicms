<?php

use UliCMS\HTML\Input;
use Michelf\MarkdownExtra;
use zz\Html\HTMLMinify;

class InfoController extends MainClass {

    const CHANGELOG_URL = "https://raw.githubusercontent.com/derUli/ulicms/master/doc/changelog.txt";

    public function changelog() {
        HTMLResult($this->_changelog());
    }

    public function _changelog(): string {
        ViewBag::set(
                "textarea_with_changelog",
                $this->_getChangelogInTextarea()
        );

        return Template::executeModuleTemplate(
                        "core_info",
                        "changelog.php"
        );
    }

    public function license() {
        HTMLResult($this->_license());
    }

    public function _license(): string {
        return Template::executeModuleTemplate(
                        "core_info",
                        "license.php"
        );
    }

    public function _fetchChangelog() {
        $changelog = file_get_contents_wrapper(self::CHANGELOG_URL);

        return ($changelog ? trim($changelog) : get_translation("fetch_failed"));
    }

    public function _getChangelogInTextarea() {
        return Input::textarea(
                        "changelog",
                        $this->_fetchChangelog(),
                        10,
                        80,
                        [
                            "readonly" => "readonly"
                        ]
        );
    }

    public function _getComposerLegalInfo(): string {
        $legalFile = Path::resolve("ULICMS_ROOT/licenses.md");
        $lastModified = filemtime($legalFile);
        
        $cacheFile = Path::resolve("ULICMS_CACHE/legal-{$lastModified}.html");
        
        if(file_exists($cacheFile)){
            return file_get_contents($cacheFile);
        }
        
        $legalText = file_get_contents($legalFile);
        
        $parser = new MarkdownExtra;
        $parser->hard_wrap = true;
        $parsed = $parser->transform($legalText);       
        $parsed = optimizeHtml($parsed, HTMLMinify::OPTIMIZATION_ADVANCED);
        
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
