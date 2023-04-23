<?php

use App\Security\XSSProtection;

class XSSProtectionTest extends \PHPUnit\Framework\TestCase {
    public function testStripTagsStripsInlineEvents() {
        $input = '<img onerror="alert(document.cookie);" src="foo.jpg"> '
                . '<p class="my-class" id="hello" onerror="foo" ' .
                'ONMouseover="foo">Foo Bar</p><div>moin</div>'
                . '<script>hax0r!</script>';
        $expected = ' Foo Barmoinhax0r!';
        $this->assertEquals($expected, XSSProtection::stripTags($input));
    }

    public function testStripTagsWithTagsStripsInlineEvents() {
        $input = '<img onerror="alert(document.cookie);" src="foo.jpg"> '
                . '<p class="my-class" id="hello" onerror="foo" ' .
                'ONMouseover="foo">Foo Bar</p><div>moin</div>'
                . '<script>hax0r!</script>';
        $expected = '<img  src="foo.jpg"> <p class="my-class" id="hello"   ' .
                'Bar</p>moinhax0r!';
        $this->assertEquals($expected, XSSProtection::stripTags($input, '<p><img>'));
    }

    public function testStripTagsWithScriptsAllowed() {
        $input = '<img onerror="alert(document.cookie);" src="foo.jpg"> '
                . '<p class="my-class" id="hello" onerror="foo" ' .
                'ONMouseover="foo">Foo Bar</p><div>moin</div>'
                . '<script>hax0r!</script>';
        $expected = '<img onerror="alert(document.cookie);" src="foo.jpg"> ' .
                'Foo Barmoin<script>hax0r!</script>';
        $this->assertEquals($expected, XSSProtection::stripTags($input, '<img><script>'));
    }
}
