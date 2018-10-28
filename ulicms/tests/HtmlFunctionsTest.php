<?php
use function UliCMS\HTML\text;

class HtmlFunctionsTest extends \PHPUnit\Framework\TestCase
{

    public function testText()
    {
        $this->assertEquals("line1<br />\nline2<br />\n&lt;strong&gt;line3&lt;/strong&gt;", text("line1\nline2\n<strong>line3</strong>"));
    }
}