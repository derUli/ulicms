<?php

use function UliCMS\HTML\text;
use function UliCMS\HTML\imageTag;
use function UliCMS\HTML\imageTagInline;
use function UliCMS\HTML\icon;
use function UliCMS\HTML\link;
use function UliCMS\HTML\button_link;
use UliCMS\Constants\LinkTarget;
use UliCMS\Constants\ButtonType;

class HtmlFunctionsTest extends \PHPUnit\Framework\TestCase {

    public function testText() {
        $this->assertEquals("line1<br />\nline2<br />\n&lt;strong&gt;line3&lt;/strong&gt;", text("line1\nline2\n<strong>line3</strong>"));
    }

    public function testImageTagWithoutAnything() {
        $this->assertEquals('<img src="/foo/bar.png">', imageTag("/foo/bar.png"));
    }

    public function testImageTagWithHtmlAttributes() {
        $this->assertEquals('<img class="my-awesome-image" title="Very awesome image" src="/foo/bar.png">', imageTag("/foo/bar.png",
                        array("class" => "my-awesome-image",
                            "title" => "Very awesome image")));
    }

    public function testImageTagInlineWithoutAnything() {

        $expectedUrl = file_get_contents(Path::resolve("ULICMS_ROOT/tests/fixtures/logo-data-url.txt"));
        $imagePath = Path::resolve("ULICMS_ROOT/admin/gfx/logo.png");

        $this->assertEquals('<img src="' . $expectedUrl . '">', imageTagInline($imagePath));
    }

    public function testImageTagInlineWithHtmlAttributes() {
        $expectedUrl = file_get_contents(Path::resolve("ULICMS_ROOT/tests/fixtures/logo-data-url.txt"));
        $imagePath = Path::resolve("ULICMS_ROOT/admin/gfx/logo.png");

        $this->assertEquals('<img class="my-awesome-image" title="Very awesome image" src="' . $expectedUrl . '">', imageTagInline($imagePath,
                        array("class" => "my-awesome-image",
                            "title" => "Very awesome image")));
    }

    public function testWithoutAdditionalAttributes() {
        $this->assertEquals('<i class="fas fa-hamburger"></i>', icon("fas fa-hamburger"));
    }

    public function testWithAdditionalAttributes() {
        $this->assertEquals('<i title="Hallo Welt" data-something="hello" class="fas fa-hamburger"></i>', icon("fas fa-hamburger", array(
            "title" => "Hallo Welt",
            "data-something" => "hello"
        )));
    }

    public function testLinkWithoutAdditionalAttributes() {
        $this->assertEquals(
                '<a href="https://www.google.de">&lt;strong&gt;Google&lt;/strong&gt;</a>',
                link("https://www.google.de", "<strong>Google</strong>", false));
    }

    public function testLinkAllowHtml() {
        $this->assertEquals(
                '<a href="https://www.google.de"><strong>Google</strong></a>',
                link("https://www.google.de", "<strong>Google</strong>", true));
    }

    public function testLinkAllowHtmlAndTarget() {
        $this->assertEquals(
                '<a href="https://www.google.de" target="_blank"><strong>Google</strong></a>',
                link("https://www.google.de", "<strong>Google</strong>", true, LinkTarget::TARGET_BLANK));
    }

    public function testLinkAllowHtmlAndTargetAndAdditionalAttributes() {
        $this->assertEquals(
                '<a id="mylink" class="btn btn-primary" href="https://www.google.de" target="_self"><strong>Google</strong></a>',
                link("https://www.google.de", "<strong>Google</strong>", true, LinkTarget::TARGET_SELF, array(
            "id" => "mylink",
            "class" => "btn btn-primary"
        )));
    }

    public function testButtonLink() {
        $this->assertEquals(
                '<a class="btn btn-info" href="https://www.google.de"><i class="fa fa fa-google"></i></a>',
                button_link("https://www.google.de", icon("fa fa fa-google"), ButtonType::TYPE_INFO, true));
    }

    public function testButtonLinkWithAttributes() {
        $this->assertEquals(
                '<a data-hello="world" class="btn btn-info awesome-button" href="https://www.google.de"><i class="fa fa fa-google"></i></a>',
                button_link("https://www.google.de", icon("fa fa fa-google"), ButtonType::TYPE_INFO, true, null,
                        array("data-hello" => "world",
                            "class" => "awesome-button")));
    }

}
