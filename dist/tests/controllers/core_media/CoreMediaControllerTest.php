<?php

use App\HTML\Link;
use Spatie\Snapshots\MatchesSnapshots;

use function App\HTML\imageTag;

class CoreMediaControllerTest extends \PHPUnit\Framework\TestCase {
    use MatchesSnapshots;

    public function testReplaceLinks(): void {
        $input = $this->getExampleHtml();

        $controller = new CoreMediaController();
        $actual = $controller->_replaceLinks($input);

        $this->assertMatchesHtmlSnapshot($actual);
    }

    public function testBeforeContentFilterEnabled(): void {
        $input = $this->getExampleHtml();

        $controller = new CoreMediaController();
        $actual = $controller->beforeContentFilter($input);
        $this->assertMatchesHtmlSnapshot($actual);
    }

    public function testReplaceLinksWithEmpty(): void {
        $controller = new CoreMediaController();
        $output = $controller->_replaceLinks('');
        $this->assertEmpty($output);
    }

    private function getExampleHtml() {
        $urls = [
            'http://example.org/',
            'https://youtu.be/7b-B1-xs6Og',
            'https://soundcloud.com/atbense/ritscheratsche-cyber-cyber-mix'
        ];

        $links = array_map(static function($url) {
            return Link::link($url, $url);
        }, $urls);

        $html = implode("\n", $links);

        $html .= imageTag(
            'foo.jpg',
            ['class' => 'foo']
        );
        $html .= imageTag(
            'foo.jpg',
            [
                'class' => 'foo',
                'loading' => 'auto'
            ]
        );

        return $html;
    }
}
