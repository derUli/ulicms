<?php

require_once Path::Resolve("ULICMS_ROOT/templating.php");

use function UliCMS\HTML\text;
use function UliCMS\HTML\imageTag;
use function UliCMS\HTML\imageTagInline;

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

        $expectedUrl = file_get_contents(dirname(__file__) . "/fixtures/logo-data-url.txt");
        $imagePath = Path::resolve("ULICMS_ROOT/admin/gfx/logo.png");

        $this->assertEquals('<img src="' . $expectedUrl . '">', imageTagInline($imagePath));
    }

    public function testImageTagInlineWithHtmlAttributes() {
        $expectedUrl = file_get_contents(dirname(__file__) . "/fixtures/logo-data-url.txt");
        $imagePath = Path::resolve("ULICMS_ROOT/admin/gfx/logo.png");

        $this->assertEquals('<img class="my-awesome-image" title="Very awesome image" src="' . $expectedUrl . '">', imageTagInline($imagePath,
                        array("class" => "my-awesome-image",
                            "title" => "Very awesome image")));
    }

}
