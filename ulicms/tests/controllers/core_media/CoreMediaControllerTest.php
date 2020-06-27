<?php

use UliCMS\HTML\Link;

class CoreMediaControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testReplaceLinks()
    {
        $input = $this->getExampleHtml();

        $controller = new CoreMediaController();
        $actual = $controller->_replaceLinks($input);
        $expected = file_get_contents(
            Path::resolve(
                    "ULICMS_ROOT/tests/fixtures/embed-media.expected.txt"
                )
        );
        $this->assertEquals(normalizeLN($actual), normalizeLN($expected));
    }

    public function testBeforeContentFilterEnabled()
    {
        $input = $this->getExampleHtml();

        $controller = new CoreMediaController();
        $actual = $controller->beforeContentFilter($input);
        $expected = file_get_contents(
            Path::resolve(
                    "ULICMS_ROOT/tests/fixtures/embed-media.expected.txt"
                )
        );
        $this->assertEquals(normalizeLN($actual), normalizeLN($expected));
    }
    
    private function getExampleHtml()
    {
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
    
    public function testReplaceLinksWithEmpty()
    {
        $controller = new CoreMediaController();
        $output = $controller->_replaceLinks("");
        $this->assertEmpty($output);
    }
}
