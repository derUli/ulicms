<?php

use App\Constants\ButtonType;
use App\Constants\LinkTarget;
use App\Exceptions\FileNotFoundException;
use Spatie\Snapshots\MatchesSnapshots;

use function App\HTML\buttonLink;
use function App\HTML\icon;
use function App\HTML\imageTag;
use function App\HTML\imageTagInline;
use function App\HTML\link;
use function App\HTML\stringContainsHtml;
use function App\HTML\text;

class HtmlFunctionsTest extends \PHPUnit\Framework\TestCase {
    use MatchesSnapshots;

    public function testText(): void {
        $this->assertEquals("line1<br />\nline2<br />\n&lt;strong&gt;line3&lt;/strong&gt;", text("line1\nline2\n<strong>line3</strong>"));
    }

    public function testImageTagWithoutAnything(): void {
        $this->assertEquals('<img src="/foo/bar.png">', imageTag('/foo/bar.png'));
    }

    public function testImageTagWithHtmlAttributes(): void {
        $this->assertEquals('<img class="my-awesome-image" title="Very awesome image" src="/foo/bar.png">', imageTag(
            '/foo/bar.png',
            ['class' => 'my-awesome-image',
                'title' => 'Very awesome image']
        ));
    }

    public function testImageTagInlineWithoutAnything(): void {
        $imagePath = \App\Utils\Path::resolve('ULICMS_ROOT/admin/gfx/logo.png');

        $this->assertMatchesHtmlSnapshot(imageTagInline($imagePath));
    }

    public function testImageTagInlineThrowsFileNotFoundException(): void {
        $this->expectException(FileNotFoundException::class);
        imageTagInline('gibts_echt_nicht.jpg');
    }

    public function testImageTagInlineWithHtmlAttributes(): void {
        $imagePath = \App\Utils\Path::resolve('ULICMS_ROOT/admin/gfx/logo.png');

        $this->assertMatchesHtmlSnapshot(
            imageTagInline(
                $imagePath,
                [
                    'class' => 'my-awesome-image',
                    'title' => 'Very awesome image'
                ]
            )
        );
    }

    public function testWithoutAdditionalAttributes(): void {
        $this->assertEquals('<i class="fas fa-hamburger"></i>', icon('fas fa-hamburger'));
    }

    public function testWithAdditionalAttributes(): void {
        $this->assertEquals('<i title="Hallo Welt" data-something="hello" class="fas fa-hamburger foobar"></i>', icon('fas fa-hamburger', [
            'title' => 'Hallo Welt',
            'data-something' => 'hello',
            'class' => 'foobar'
        ]));
    }

    public function testLinkWithoutAdditionalAttributes(): void {
        $this->assertEquals(
            '<a href="https://www.google.de">&lt;strong&gt;Google&lt;/strong&gt;</a>',
            link('https://www.google.de', '<strong>Google</strong>', false)
        );
    }

    public function testLinkAllowHtml(): void {
        $this->assertEquals(
            '<a href="https://www.google.de"><strong>Google</strong></a>',
            link('https://www.google.de', '<strong>Google</strong>', true)
        );
    }

    public function testLinkAllowHtmlAndTarget(): void {
        $this->assertEquals(
            '<a href="https://www.google.de" target="_blank"><strong>Google</strong></a>',
            link('https://www.google.de', '<strong>Google</strong>', true, LinkTarget::TARGET_BLANK)
        );
    }

    public function testLinkAllowHtmlAndTargetAndAdditionalAttributes(): void {
        $this->assertEquals(
            '<a id="mylink" class="btn btn-primary" href="https://www.google.de" target="_self"><strong>Google</strong></a>',
            link('https://www.google.de', '<strong>Google</strong>', true, LinkTarget::TARGET_SELF, [
                'id' => 'mylink',
                'class' => 'btn btn-primary'
            ])
        );
    }

    public function testButtonLink(): void {
        $this->assertEquals(
            '<a class="btn btn-info" href="https://www.google.de"><i class="fa fa fa-google"></i></a>',
            buttonLink('https://www.google.de', icon('fa fa fa-google'), ButtonType::TYPE_INFO, true)
        );
    }

    public function testButtonLinkWithAttributes(): void {
        $this->assertEquals(
            '<a data-hello="world" class="btn btn-info awesome-button" href="https://www.google.de"><i class="fa fa fa-google"></i></a>',
            buttonLink(
                'https://www.google.de',
                icon('fa fa fa-google'),
                ButtonType::TYPE_INFO,
                true,
                null,
                ['data-hello' => 'world',
                    'class' => 'awesome-button']
            )
        );
    }

    public function testStringContainsHtmlReturnsTrue(): void {
        $this->assertTrue(stringContainsHtml('Hallo <script>alert("xss");</script> Welt!'));
    }

    public function testStringContainsHtmlReturnsFalse(): void {
        $this->assertFalse(stringContainsHtml('Hallo Welt'));
    }
}
