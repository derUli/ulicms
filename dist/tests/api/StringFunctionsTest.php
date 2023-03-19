<?php

class StringFunctionsTest extends \PHPUnit\Framework\TestCase
{
    public function testStrBoolTrue()
    {
        $this->assertEquals("true", strbool(true));
        $this->assertEquals("true", strbool(1));
    }

    public function testStrBoolFalse()
    {
        $this->assertEquals("false", strbool(false));
        $this->assertEquals("false", strbool(0));
        $this->assertEquals("false", strbool(null));
    }

    public function testConvertLineEndingsToLN()
    {
        $input = "Line1\r\nLine2\r\nLine3\n";
        $expected = "Line1\nLine2\nLine3\n";
        $this->assertEquals($expected, convertLineEndingsToLN($input));
    }

    public function testSanitize()
    {
        $input = array(
            "My\r\nWorld\r",
            " entferne%0adas",
            " entferne%0adas",
            "%0dlorem ipsum %0d"
        );

        sanitize($input);
        $this->assertCount(4, $input);
        $this->assertEquals("MyWorld", $input[0]);
        $this->assertEquals(" entfernedas", $input[1]);
        $this->assertEquals(" entfernedas", $input[2]);
        $this->assertEquals("lorem ipsum ", $input[3]);
    }

    public function testUnEsc()
    {
        $input = '&lt;span style=&quot;color:red&quot;&gt;This is HTML&lt;/span&gt;';
        $expected = '<span style="color:red">This is HTML</span>';
        $this->assertEquals($expected, _unesc($input));
    }

    public function testUnEscWithEcho()
    {
        $input = '&lt;span style=&quot;color:red&quot;&gt;This is HTML&lt;/span&gt;';
        $expected = '<span style="color:red">This is HTML</span>';
        ob_start();
        unesc($input);
        $this->assertEquals($expected, ob_get_clean());
    }

    public function testNormalizeLn()
    {
        $input = "Line1\nLine2\r\nLine3\r\n";
        $this->assertEquals("Line1\r\nLine2\r\nLine3\r\n", normalizeLN($input));
        $this->assertEquals("Line1\r\nLine2\r\nLine3\r\n", normalizeLN($input, "\r\n"));
        $this->assertEquals("Line1\nLine2\nLine3\n", normalizeLN($input, "\n"));
    }

    public function testMbStrSplit()
    {
        $input = "Dieser Hügel ist der höchste.";
        $result = mb_str_split($input);
        $this->assertCount(29, mb_str_split($input));
        $this->assertEquals("ü", $result[8]);
        $this->assertEquals("i", $result[1]);
        $this->assertEquals("ö", $result[22]);
    }

    public function testStringOrNullExpectString()
    {
        $this->assertEquals("foo", stringOrNull("foo"));
        $this->assertEquals(" foo ", stringOrNull(" foo "));
    }

    public function testStringOrNullExpectNull()
    {
        $this->assertNull(stringOrNull(''));
        $this->assertNull(stringOrNull(null));
    }

    public function testMakeLinksClickable()
    {
        $input = "Das hier ist ein Text.
http://www.google.de
Noch mehr Text http://www.ulicms.de und so weiter.";

        $expected = 'Das hier ist ein Text.
<a href="http://www.google.de" rel="nofollow" target="_blank">http://www.google.de</a>
Noch mehr Text <a href="http://www.ulicms.de" rel="nofollow" target="_blank">http://www.ulicms.de</a> und so weiter.';

        $this->assertEquals($expected, make_links_clickable($input));
    }

    public function testCleanString()
    {
        $this->assertEquals("hello-world", cleanString("Hello World"));
        $this->assertEquals("hello-world", cleanString("Hello World", "-"));
        $this->assertEquals("hello_world", cleanString("Hello World", "_"));
        $this->assertEquals(
            "das-ist-die-grossfraesmaschinenoeffnungstuer",
            cleanString("Das ist die Großfräsmaschinenöffnungstür.")
        );
    }

    public function testEsc()
    {
        ob_start();
        esc("<script>alert('xss')</script>");
        $this->assertEquals(
            "&lt;script&gt;alert(&#039;xss&#039;)&lt;/script&gt;",
            ob_get_clean()
        );
    }

    public function testGetExcerptReturnsShortedString()
    {
        $this->assertEquals(
            "Lorem Ipsum...",
            getExcerpt("Lorem Ipsum sit dor amet usw.", 0, 16)
        );
    }
}
