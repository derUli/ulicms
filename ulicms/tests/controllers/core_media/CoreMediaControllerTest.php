<?php

use UliCMS\HTML\Link;
use Spatie\Snapshots\MatchesSnapshots;

class CoreMediaControllerTest extends \PHPUnit\Framework\TestCase {

    use MatchesSnapshots;

    public function testReplaceLinks() {
        $input = $this->getExampleHtml();

        $controller = new CoreMediaController();
        $actual = $controller->_replaceLinks($input);

        $this->assertMatchesHtmlSnapshot($actual);
    }

    public function testBeforeContentFilterEnabled() {
        $input = $this->getExampleHtml();

        $controller = new CoreMediaController();
        $actual = $controller->beforeContentFilter($input);
        $this->assertMatchesHtmlSnapshot($actual);
    }

    private function getExampleHtml() {
        $urls = [
            "http://example.org/",
            "https://youtu.be/7b-B1-xs6Og",
            "https://soundcloud.com/atbense/ritscheratsche-cyber-cyber-mix"
        ];

        $links = array_map(function ($url) {
            return Link::link($url, $url);
        }, $urls);

        return implode("\n", $links);
    }

    public function testReplaceLinksWithEmpty() {
        $controller = new CoreMediaController();
        $output = $controller->_replaceLinks("");
        $this->assertEmpty($output);
    }

}
